<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GatewayController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Gateways\PaypalController;
use App\Jobs\SendInviteEmail;
use App\Models\Activity;
use App\Models\Gateways;
use App\Models\OpenAIGenerator;
use App\Models\OpenaiGeneratorFilter;
use App\Models\PaymentPlans;
use App\Models\Setting;
use App\Models\Subscriptions as SubscriptionsModel;
use App\Models\Template;
use App\Models\YokassaSubscriptions as YokassaSubscriptionsModel;
use App\Models\User;
use App\Models\UserAffiliate;
use App\Models\UserFavorite;
use App\Models\UserOpenai;
use App\Models\UserOpenaiChat;
use App\Models\UserOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laravel\Cashier\Payment;
use Stripe\PaymentIntent;
use Stripe\Plan;
use enshrined\svgSanitize\Sanitizer;
use Barryvdh\DomPDF\Facade\Pdf;
use FontLib\Font;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Response as FacadesResponse;
use Response;

class UserController extends Controller
{
    public function redirect()
    {
        if (Auth::user()->type == 'admin') {
            return redirect()->route('dashboard.admin.index');
        } else {
            return redirect()->route('dashboard.user.index');
        }
    }

    public function index()
    {
        $ongoingPayments = null;
        // $ongoingPayments = self::prepareOngoingPaymentsWarning();
        // $user = Auth::user();
        $tmp = PaymentController::checkUnmatchingSubscriptions();

        return view('panel.user.dashboard', compact('ongoingPayments')); //
    }

    public function prepareOngoingPaymentsWarning()
    {
        $ongoingPayments = PaymentController::checkForOngoingPayments();
        if ($ongoingPayments != null) {
            return $ongoingPayments;
        }
        return null;
    }

    public function openAIList()
    {
        $list = OpenAIGenerator::all();
        $filters = OpenaiGeneratorFilter::get();
        $templates = Template::all();
        return view('panel.user.openai.list', compact('list', 'filters', 'templates'));
    }

    public function openAIFavoritesList()
    {
        return view('panel.user.openai.list_favorites');
    }

    public function openAIFavorite(Request $request)
    {
        $exists =  isFavorited($request->id);
        if ($exists) {
            $favorite = UserFavorite::where('openai_id', $request->id)->where('user_id', Auth::id())->first();
            $favorite->delete();
            $html = '<svg width="16" height="15" viewBox="0 0 16 15" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg">
						<path d="M7.99989 11.8333L3.88522 13.9966L4.67122 9.41459L1.33789 6.16993L5.93789 5.50326L7.99522 1.33459L10.0526 5.50326L14.6526 6.16993L11.3192 9.41459L12.1052 13.9966L7.99989 11.8333Z" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>';
        } else {
            $favorite = new UserFavorite();
            $favorite->user_id = Auth::id();
            $favorite->openai_id = $request->id;
            $favorite->save();
            $html = '<svg width="16" height="15" viewBox="0 0 16 15" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
						<path d="M7.99989 11.8333L3.88522 13.9966L4.67122 9.41459L1.33789 6.16993L5.93789 5.50326L7.99522 1.33459L10.0526 5.50326L14.6526 6.16993L11.3192 9.41459L12.1052 13.9966L7.99989 11.8333Z" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>';
        }
        return response()->json(compact('html'));
    }

    public function openAIGenerator($slug)
    {
        $openai = OpenAIGenerator::whereSlug($slug)->firstOrFail();
        $userOpenai = UserOpenai::where('user_id', Auth::id())->where('openai_id', $openai->id)->orderBy('created_at', 'desc')->paginate(10);
        return view('panel.user.openai.generator', compact('openai', 'userOpenai'));
    }

    public function openAIGeneratorWorkbook($slug)
    {
        $openai = OpenAIGenerator::whereSlug($slug)->firstOrFail();
        $settings = Setting::first();
        $templates = Template::all();

        // Fetch the Site Settings object with openai_api_secret
        $apiKeys = explode(',', $settings->openai_api_secret);
        $apiKey = $apiKeys[array_rand($apiKeys)];

        $len = strlen($apiKey);
        $parts[] = substr($apiKey, 0, $l[] = rand(1, $len - 5));
        $parts[] = substr($apiKey, $l[0], $l[] = rand(1, $len - $l[0] - 3));
        $parts[] = substr($apiKey, array_sum($l));
        $apikeyPart1 = base64_encode($parts[0]);
        $apikeyPart2 = base64_encode($parts[1]);
        $apikeyPart3 = base64_encode($parts[2]);
        $apiUrl = base64_encode('https://api.openai.com/v1/chat/completions');

        return view('panel.user.openai.generator_workbook', compact(
            'openai',
            'apikeyPart1',
            'apikeyPart2',
            'apikeyPart3',
            'apiUrl',
            'templates',
        ));
    }

    public function openAIGeneratorWorkbookSave(Request $request)
    {
        $workbook = UserOpenai::where('slug', $request->workbook_slug)->firstOrFail();
        $workbook->output = $request->workbook_text;
        $workbook->title = $request->workbook_title;
        $workbook->save();
        return response()->json([], 200);
    }

    public function generateQrPdf(Request $request)
    {
        try {

            $content = str_replace(['<p>', '</p>'], '', $request->content);
            $arrayContent = explode('<br>',$content);
            $filteredContent = Arr::flatten(array_filter($arrayContent));

            $template = Template::find($request->template_id);
            $html = $this->shortcode_replace($template->code, $filteredContent, $request);

            $pdf = PDF::loadHTML($html)
                        ->setPaper('A4', 'portrait')
                        ->setOption([
                            'isHtml5ParserEnabled' => true,
                            'isRemoteEnabled' => true,
                            'defaultFont' => 'times',
                        ])->setWarnings(false);

            $filename = Str::slug( $filteredContent[0], '_').'_'.uniqid() . '.pdf';

            // Storage path for PDF
            $storagePath = 'public/assets/pdf/';

            // Create directory if it doesn't exist
            if (!Storage::exists($storagePath)) {
                Storage::makeDirectory($storagePath);
            }

            // Save the PDF to storage
            Storage::put($storagePath . $filename, $pdf->output());

            // Move the saved file to the desired location
            $sourceFilePath = $storagePath . $filename;
            $destinationFilePath = 'public/assets/pdf/' . $filename; // Modify the destination as needed

            Storage::move($sourceFilePath, $destinationFilePath);

            // Update UserOpenai model
            UserOpenai::where('id', $request->obituary_id)->update([
                'qrCodeId' => md5(uniqid(rand(), true)),
                'template' => $destinationFilePath,
            ]);

            return response()->json(['status' => true, 'obituary' => UserOpenai::find($request->obituary_id)],200); // Indicating success
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()],400); // Indicating failure
        }

    }


    public function downloadPdf($qrCodeId)
    {
        $obituary = UserOpenai::where('qrCodeId', $qrCodeId)->first();
        return Storage::download($obituary->template);
    }

    public function previewPdf($id, $type = null)
    {
        if($type == null){
            $templatePath = Template::find($id)?->filePath;
        }else if($type != null){
            $templateFile = UserOpenai::where('qrCodeId', $id)?->first()?->template;
            $templatePath = Storage::url($templateFile);
        }

        return view('page.pdfviewer', get_defined_vars());
    }


    public function shortcode_replace($template, $content, $request)
    {
        $templateHtml = $template;
        // $shortcodeImage = '';

        // foreach($request->images as $i => $image){
        //      // Open a new row for every even index
        //     if (($i + 1) % 2 == 1) {
        //         $shortcodeImage .= '<tr>';
        //     }
        //     $shortcodeImage .= '<td>
        //                             <img src="data:'.$image->getMimeType().';base64,' . base64_encode(file_get_contents($image)).'" alt="Cinque Terre" width="200px" height="200px">
        //                         </td>';

        //      // Close the row for every odd index or when it's the last element
        //     if (($i + 1) % 2 == 0 || $i == count($request->images) - 1) {
        //         $shortcodeImage .= '</tr>';
        //     }
        // }

        //Replace image with shortcodes
        if(isset($request->profile_image)){
            if(str_contains($templateHtml, '{PROFILE-IMAGE}')) {
                $templateHtml = str_replace("{PROFILE-IMAGE}", 'data:'.$request->profile_image->getMimeType().';base64,' . base64_encode(file_get_contents($request->profile_image)), $templateHtml);
            }
        }else{
            $templateHtml = str_replace("{PROFILE-IMAGE}", " ", $templateHtml);
        }


        // if(isset($request->images)){
        //     if(str_contains($templateHtml, '{IMAGES}')) {
        //         $templateHtml = str_replace("{IMAGES}", $shortcodeImage, $templateHtml);
        //     }
        // }else{
        //     $templateHtml = str_replace("{IMAGES}", " ", $templateHtml);
        // }

        //Replace text with shortcodes
        if(isset($request->obituary_name)){
            if(str_contains($templateHtml, '{NAME}')) {
                $templateHtml = str_replace("{NAME}", $request->obituary_name, $templateHtml);
            }
        }else{
            $templateHtml = str_replace("{NAME}", " ", $templateHtml);
        }

        if(isset($request->obituary_dob)){
            if(str_contains($templateHtml, '{DATE}')) {
                $templateHtml = str_replace("{DATE}", $request->obituary_dob, $templateHtml);
            }
        }else{
            $templateHtml = str_replace("{DATE}", " ", $templateHtml);
        }

        if(isset($content[2])){
            if(str_contains($templateHtml, '{BIRTH-PLACE}')) {
                $templateHtml = str_replace("{BIRTH-PLACE}", $content[2], $templateHtml);
            }
        }else{
            $templateHtml = str_replace("{BIRTH-PLACE}", " ", $templateHtml);
        }

        if(isset($content[3])){
            if(str_contains($templateHtml, '{FAMILY-MEMBER}')) {
                $templateHtml = str_replace("{FAMILY-MEMBER}", $content[3], $templateHtml);
            }
        }else{
                $templateHtml = str_replace("{FAMILY-MEMBER}", " ", $templateHtml);
        }

        if(isset($content[4])){
            if(str_contains($templateHtml, '{EDUCATION}')) {
                $templateHtml = str_replace("{EDUCATION}", $content[4], $templateHtml);
            }
        }else{
            $templateHtml = str_replace("{EDUCATION}", " ", $templateHtml);
        }

        if(isset($content[5])){
            if(str_contains($templateHtml, '{CAREER-WORK-HISTORY}')) {
                $templateHtml = str_replace("{CAREER-WORK-HISTORY}", $content[5], $templateHtml);
            }
        }else{
            $templateHtml = str_replace("{CAREER-WORK-HISTORY}", " ", $templateHtml);
        }

        if(isset($content[6])){
            if(str_contains($templateHtml, '{HOBBIES}')) {
                $templateHtml = str_replace("{HOBBIES}", $content[6], $templateHtml);
            }
        }else{
            $templateHtml = str_replace("{HOBBIES}", " ", $templateHtml);
        }

        if(isset($content[7])){
            if(str_contains($templateHtml, '{COMMUNITY}')) {
                $templateHtml = str_replace("{COMMUNITY}", $content[7], $templateHtml);
            }
        }else{
            $templateHtml = str_replace("{COMMUNITY}", " ", $templateHtml);
        }

        if(isset($content[8])){
            if(str_contains($templateHtml, '{RELIGIOUS}')) {
                $templateHtml = str_replace("{RELIGIOUS}", $content[8], $templateHtml);
            }
        }else{
            $templateHtml = str_replace("{RELIGIOUS}", " ", $templateHtml);
        }

        if(isset($content[9])){
            if(str_contains($templateHtml, '{MEMORIAL}')) {
                $templateHtml = str_replace("{MEMORIAL}", $content[9], $templateHtml);
            }
        }else{
            $templateHtml = str_replace("{MEMORIAL}", " ", $templateHtml);
        }

        return $templateHtml;
    }

    //Chat
    public function openAIChat()
    {
        $chat = Auth::user()->openaiChat;
        return view('panel.user.openai.chat', compact('chat'));
    }



    public static function sanitizeSVG($uploadedSVG)
    {

        $sanitizer = new Sanitizer();
        $content = file_get_contents($uploadedSVG);
        $cleanedData = $sanitizer->sanitize($content);
        $added = file_put_contents($uploadedSVG, $cleanedData);
        return $uploadedSVG;
    }

    //Profile user settings
    public function userSettings()
    {
        $user = Auth::user();
        return view('panel.user.settings.index', compact('user'));
    }

    public function userSettingsSave(Request $request)
    {
        $user = Auth::user();
        $user->name = $request->name;
        $user->surname = $request->surname;
        $user->phone = $request->phone;
        $user->country = $request->country;

        if ($request->old_password != null) {
            $validated = $request->validateWithBag('updatePassword', [
                'old_password' => ['required', 'current_password'],
                'new_password' => ['required', Password::defaults(), 'confirmed'],
            ]);

            $user->password = Hash::make($request->new_password);
        }

        if ($request->hasFile('avatar')) {
            $path = 'upload/images/avatar/';
            $image = $request->file('avatar');

            if ($image->getClientOriginalExtension() == 'svg') {
                $image = self::sanitizeSVG($request->file('avatar'));
            }

            $image_name = Str::random(4) . '-' . Str::slug($user->fullName()) . '-avatar.' . $image->getClientOriginalExtension();

            //Image extension check
            $imageTypes = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
            if (!in_array(Str::lower($image->getClientOriginalExtension()), $imageTypes)) {
                $data = array(
                    'errors' => ['The file extension must be jpg, jpeg, png, webp or svg.'],
                );
                return response()->json($data, 419);
            }

            $image->move($path, $image_name);

            $user->avatar = $path . $image_name;
        }

        createActivity($user->id, 'Updated', 'Profile Information', null);
        $user->save();
    }

    //Purchase
    public function subscriptionPlans()
    {

        //check if any payment gateway enabled
        $activeGateways = Gateways::where("is_active", 1)->get();
        if ($activeGateways->count() > 0) {
            $is_active_gateway = 1;
        } else {
            $is_active_gateway = 0;
        }

        //check if any subscription is active
        $userId = Auth::user()->id;
        // Get current active subscription
        $activeSub = SubscriptionsModel::where([['stripe_status', '=', 'active'], ['user_id', '=', $userId]])->orWhere([['stripe_status', '=', 'trialing'], ['user_id', '=', $userId]])->first();
        $activesubid = 0; //id can't be zero, so this will be easy to check
        if ($activeSub != null) {
            $activesubid = $activeSub->plan_id;
        }

        $activeSub_yokassa = YokassaSubscriptionsModel::where([['subscription_status', '=', 'active'],['user_id','=', $userId]])->first();
        if($activeSub_yokassa != null) {
            $activesubid = $activeSub_yokassa->plan_id;
        }

        $plans = PaymentPlans::where('type', 'subscription')->where('active', 1)->get();
        $prepaidplans = PaymentPlans::where('type', 'prepaid')->where('active', 1)->get();
        return view('panel.user.payment.subscriptionPlans', compact('plans', 'prepaidplans', 'is_active_gateway', 'activeGateways', 'activesubid'));
    }

    //Invoice - Billing
    public function invoiceList()
    {
        $user = Auth::user();
        $list = $user->orders;
        return view('panel.user.orders.index', compact('list'));
    }

    public function invoiceSingle($order_id)
    {
        $user = Auth::user();
        $invoice = UserOrder::where('order_id', $order_id)->firstOrFail();
        return view('panel.user.orders.invoice', compact('invoice'));
    }

    public function documentsAll()
    {
        $items = Auth::user()
        ->openai()
        ->whereHas('generator', function ($query) {
            $query->where('type', '!=', 'image');
        })
        ->orderBy('created_at', 'desc')->paginate(50);
        return view('panel.user.openai.documents', compact('items'));
    }

    public function documentsSingle($slug)
    {
        $workbook = UserOpenai::where('slug', $slug)->first();
        $openai = $workbook->generator;
        return view('panel.user.openai.documents_workbook', compact('workbook', 'openai'));
    }

    public function documentsDelete($slug)
    {
        $workbook = UserOpenai::where('slug', $slug)->first();
        $image = $workbook->response;
        $storage = $workbook->storage;
        try {
            if($storage == 's3') {
                Storage::disk('s3')->delete(basename($image));
            } else {

                    unlink(substr($image, 1));

            }
        } catch (\Throwable $th) {}
        $workbook->delete();
        return redirect()->route('dashboard.user.openai.documents.all')->with(['message' => 'Document deleted successfuly', 'type' => 'success']);
    }

    public function documentsImageDelete($slug)
    {
        $workbook = UserOpenai::where('slug', $slug)->first();
        if ($workbook->storage == UserOpenai::STORAGE_LOCAL) {
            $file = str_replace('/uploads/', "", $workbook->output);
            Storage::disk('public')->delete($file);
        } else if ($workbook->storage == UserOpenai::STORAGE_AWS) {
            $file = str_replace('/', '', parse_url($workbook->output)['path']);
            Storage::disk('s3')->delete($file);
        } else {

            // Manual deleting depends on response
            if (str_contains($workbook->output, 'https://')) {
                // AWS Storage
                $file = str_replace('/', '', parse_url($workbook->output)['path']);
                Storage::disk('s3')->delete($file);
            } else {
                $file = str_replace('/uploads/', "", $workbook->output);
                Storage::disk('public')->delete($file);
            }

        }
        $workbook->delete();
        return back()->with(['message' => 'Deleted successfuly', 'type' => 'success']);
    }

    //Affiliates
    public function affiliatesList()
    {
        $user = Auth::user();
        $list = $user->affiliates;
        $list2 = $user->withdrawals;
        $totalEarnings = 0;
        foreach ($list as $affOrders) {
            $totalEarnings += $affOrders->orders->sum('affiliate_earnings');
        }
        $totalWithdrawal = 0;
        foreach ($list2 as $affWithdrawal) {
            $totalWithdrawal += $affWithdrawal->amount;
        }
        return view('panel.user.affiliate.index', compact('list', 'list2', 'totalEarnings', 'totalWithdrawal'));
    }

    public function affiliatesListSendInvitation(Request $request)
    {
        $user = Auth::user();

        $sendTo = $request->to_mail;

        dispatch(new SendInviteEmail($user, $sendTo));

        return response()->json([], 200);
    }

    public function affiliatesListSendRequest(Request $request)
    {
        $user = Auth::user();
        $list = $user->affiliates;
        $list2 = $user->withdrawals;

        $totalEarnings = 0;
        foreach ($list as $affOrders) {
            $totalEarnings += $affOrders->orders->sum('affiliate_earnings');
        }
        $totalWithdrawal = 0;
        foreach ($list2 as $affWithdrawal) {
            $totalWithdrawal += $affWithdrawal->amount;
        }
        if ($totalEarnings - $totalWithdrawal >= $request->amount) {
            $user->affiliate_bank_account = $request->affiliate_bank_account;
            $user->save();
            $withdrawalReq = new UserAffiliate();
            $withdrawalReq->user_id = Auth::id();
            $withdrawalReq->amount = $request->amount;
            $withdrawalReq->save();

            createActivity($user->id, 'Sent', 'Affiliate Withdraw Request', route('dashboard.admin.affiliates.index'));
        } else {
            return response()->json(['error' => 'ERROR'], 411);
        }
    }
}
