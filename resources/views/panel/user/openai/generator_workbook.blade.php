@extends('panel.layout.app')
@section('title', __('AI Code'))

@section('additional_css')
    <style>
        .outer-div {
            padding-left: 10px;
            padding-right: 10px;
            padding-top: 4px;
            padding-bottom: 4px;
            background: rgba(28.32, 165.75, 132.77, 0.20);
            border-radius: 7px;
            justify-content: flex-start;
            align-items: center;
            gap: 6px;
            display: inline-flex
        }

        .inner-div {
            color: #20725E;
            font-size: 12px;
            font-family: SF Pro Text;
            font-weight: 500;
            word-wrap: break-word;
        }

        /* ------- Multi Step Tabs -------- */
        #svg_form_time {
            height: 15px;
            max-width: 80%;
            margin: 40px auto 20px;
            display: block;
        }

        #svg_form_time circle,
        #svg_form_time rect {
            fill: white;
        }

        .disabled {
            display: none;
        }

        /*----------multiple-file-upload-----------*/
        @import url('https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900&display=swap');

        button:focus,
        input:focus {
            outline: none;
            box-shadow: none;
        }

        .input-group.file-caption-main {
            display: none;
        }

        .close.fileinput-remove {
            display: none;
        }

        .file-drop-zone {
            margin: 0px;
            border: 1px solid #fff;
            background-color: #fff;
            padding: 0px;
            display: contents;
        }

        .file-drop-zone.clickable:hover {
            border-color: #fff;
        }

        .file-drop-zone .file-preview-thumbnails {
            display: inline;
        }

        .file-drop-zone-title {
            padding: 15px !important;
            height: 120px !important;
            width: 120px !important;
            font-size: 12px !important;
        }

        .file-input-ajax-new {
            display: inline-block;
        }

        .file-input.theme-fas {
            display: inline-block;
            width: 100%;
        }

        .file-preview {
            padding: 0px !important;
            border: none !important;
            display: inline !important;
        }

        .file-drop-zone-title {
            display: none;
        }

        .file-footer-caption {
            display: none !important;
        }

        .kv-file-upload {
            display: none;
        }

        .file-upload-indicator {
            display: none;
        }

        .file-drag-handle.drag-handle-init.text-info {
            display: none;
        }

        .krajee-default.file-preview-frame .kv-file-content {
            width: 90px !important;
            height: 90px !important;
            display: flex;
            text-align: center;
            align-items: center;
        }

        .krajee-default.file-preview-frame {
            background-color: #fff;
            margin: 3px !important;
            border-radius: 15px !important;
            overflow: hidden;
        }

        .krajee-default.file-preview-frame:not(.file-preview-error):hover {
            box-shadow: none !important;
            border-color: #ed3237 !important;
        }

        .krajee-default.file-preview-frame:not(.file-preview-error):hover .file-preview-image {
            transform: scale(1.1);
        }

        .krajee-default.file-preview-frame {
            box-shadow: none;
            border-color: #fff;
            max-width: 150px !important;
            margin: 5px !important;
            padding: 0px !important;
            transition: 0.5s !important;
        }

        .file-thumbnail-footer,
        .file-actions {
            width: 20px !important;
            height: 20px !important;
            position: absolute !important;
            top: 3px !important;
            right: 3px !important;
        }

        .kv-file-remove:focus,
        .kv-file-remove:active {
            outline: none !important;
            box-shadow: none !important;
        }

        .kv-file-remove {
            border-radius: 50% !important;
            z-index: 1;
            right: 0;
            position: absolute;
            top: 0;
            text-align: center;
            color: #fff;
            background-color: #ed3237;
            border: 1px solid #ed3237;
            padding: 2px 6px !important;
            font-size: 11px !important;
            transition: 0.5s !important;
        }

        .kv-file-remove:hover {
            border-color: #fdeff0;
            background-color: #fdeff0;
            color: #ed1924;
        }

        .kv-preview-data.file-preview-video {
            width: 100% !important;
            height: 100% !important;
        }

        .btn-outline-secondary.focus,
        .btn-outline-secondary:focus {
            box-shadow: none;
        }

        .btn-toggleheader,
        .btn-fullscreen,
        .btn-borderless {
            display: none;
        }

        .btn-kv.btn-close {
            color: #fff;
            border: none;
            background-color: #ed3237;
            font-size: 11px !important;
            width: 18px !important;
            height: 18px !important;
            text-align: center;
            padding: 0px !important;
        }

        .btn-outline-secondary:not(:disabled):not(.disabled).active:focus,
        .btn-outline-secondary:not(:disabled):not(.disabled):active:focus,
        .show>.btn-outline-secondary.dropdown-toggle:focus {
            background-color: rgba(255, 255, 255, 0.8);
            color: #000;
            box-shadow: none;
            color: #ed3237;
        }

        .kv-file-content .file-preview-image {
            width: 90px !important;
            height: 90px !important;
            max-width: 90px !important;
            max-height: 90px !important;
            transition: 0.5s;
        }

        .btn-danger.btn-file {
            padding: 0px;
            height: 95px;
            width: 95px;
            display: inline-block;
            margin: 5px;
            border-color: #fdeff0;
            background-color: #fdeff0;
            color: #ed1924;
            border-radius: 15px;
            padding-top: 30px;
            transition: 0.5s;
        }

        .btn-danger.btn-file:active,
        .btn-danger.btn-file:hover {
            background-color: #fde3e5;
            color: #ed1924;
            border-color: #fdeff0;
            box-shadow: none;
        }

        .btn-danger.btn-file i {
            font-size: 30px;
        }

        @media (max-width: 350px) {
            .krajee-default.file-preview-frame:not([data-template=audio]) .kv-file-content {
                width: 90px;
            }
        }
    </style>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-image-checkbox.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.8/css/fileinput.min.css" />

@endsection

@section('content')
    <div class="page-header">
        <div class="container-xl">
            <div class="row g-2 items-center">
                <div class="col">
                    <div class="page-pretitle">
                        @if ($openai->type == 'code')
                            {{ __('Generate high quality code in seconds.') }}
                        @elseif(isset($openai->description))
                            {{ __($openai->description) }}
                        @endif
                    </div>
                    <h2 class="page-title mb-2">
                        {{ __($openai->title) }}
                    </h2>
                </div>

            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body pt-6">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-sm-6 col-lg-5 lg:pr-14">
                    <div class="card mb-[25px]">
                        <div class="card-body">
                            <h5 class="mb-3 text-[14px] font-normal">{{ __('Remaining Credits') }}</h5>
                            <div class="progress progress-separated mb-2">
                                @if ((int) Auth::user()->remaining_words + (int) Auth::user()->remaining_images != 0)
                                    <div class="progress-bar grow-0 shrink-0 basis-auto bg-primary" role="progressbar"
                                        style="width: {{ ((int) Auth::user()->remaining_words / ((int) Auth::user()->remaining_words + (int) Auth::user()->remaining_images)) * 100 }}%"
                                        aria-label="{{ __('Text') }}"></div>
                                @endif
                                @if ((int) Auth::user()->remaining_words + (int) Auth::user()->remaining_images != 0)
                                    <div class="progress-bar grow-0 shrink-0 basis-auto bg-[#9E9EFF]" role="progressbar"
                                        style="width: {{ ((int) Auth::user()->remaining_images / ((int) Auth::user()->remaining_words + (int) Auth::user()->remaining_images)) * 100 }}%"
                                        aria-label="{{ __('Images') }}"></div>
                                @endif
                            </div>
                            <div class="flex justify-between flex-wrap">
                                <div class="d-flex align-items-center">
                                    <span class="legend !me-2 rounded-full bg-primary"></span>
                                    <span>{{ __('Words') }}</span>
                                    <span class="ms-2 text-heading font-medium">
                                        @if (Auth::user()->remaining_words == -1)
                                            {{ __('Unlimited') }}
                                        @else
                                            {{ number_format((int) Auth::user()->remaining_words) }}
                                        @endif
                                    </span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="legend !me-2 rounded-full bg-[#9E9EFF]"></span>
                                    <span>{{ __('Images') }}</span>
                                    <span class="ms-2 text-heading font-medium">
                                        @if (Auth::user()->remaining_images == -1)
                                            {{ __('Unlimited') }}
                                        @else
                                            {{ number_format((int) Auth::user()->remaining_images) }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form class="row" id="openai_generator_form" onsubmit="return sendOpenaiGeneratorForm();">

                        @foreach (json_decode($openai->questions) as $question)
                            <div class="mb-3 col-xs-12">
                                @php
                                    $placeholder = isset($question->description) && !empty($question->description) ? __($question->description) : __($question->question);
                                @endphp
                                @if ($question->type == 'text')
                                    <label class="form-label">{{ __($question->question) }}</label>
                                    <input type="{{ $question->type }}" class="form-control" id="{{ $question->name }}"
                                        name="{{ $question->name }}" maxlength="{{ $setting->openai_max_input_length }}"
                                        placeholder="{{ $placeholder }}" required="required">
                                @elseif($question->type == 'textarea')
                                    <label class="form-label">{{ __($question->question) }}</label>
                                    <textarea class="form-control" id="{{ $question->name }}" name="{{ $question->name }}" rows="12"
                                        placeholder="{{ $placeholder }}" maxlength="{{ $setting->openai_max_input_length }}" required="required"></textarea>
                                @elseif($question->type == 'select')
                                    <div class="form-label">{{ __($question->question) }}</div>
                                    <select class="form-select" id="{{ $question->name }}" name="{{ $question->name }}"
                                        required="required">
                                        {!! $question->select !!}
                                    </select>
                                @endif
                            </div>
                        @endforeach

                        @if ($openai->type == 'text')
                            <div class="mb-3 col-xs-12">
                                <label class="form-label">{{ __('Language') }}</label>
                                <select type="text" class="form-select" name="language" id="language" required>
                                    @include('panel.user.openai.components.countries')
                                </select>
                            </div>

                            <div class="mb-3 col-xs-12 col-md-6">
                                <label class="form-label">{{ __('Maximum Length') }}</label>
                                <input type="number" class="form-control" id="maximum_length" name="maximum_length"
                                    max="{{ $setting->openai_max_output_length }}" value="400"
                                    placeholder="{{ __('Maximum character length of text') }}" required>
                            </div>

                            <div class="mb-3 col-xs-12 col-md-6">
                                <label class="form-label">{{ __('Number of Results') }}</label>
                                <input type="number" class="form-control" id="number_of_results" name="number_of_results"
                                    value="1" placeholder="{{ __('Number of results') }}" required>
                            </div>

                            <div class="mb-3 col-xs-12 col-md-6">
                                <label class="form-label">{{ __('Creativity') }}</label>
                                <select type="text" class="form-select" name="creativity" id="creativity" required>
                                    <option value="0.25"
                                        {{ $setting->openai_default_creativity == 0.25 ? 'selected' : '' }}>
                                        {{ __('Economic') }}</option>
                                    <option value="0.5"
                                        {{ $setting->openai_default_creativity == 0.5 ? 'selected' : '' }}>
                                        {{ __('Average') }}</option>
                                    <option value="0.75"
                                        {{ $setting->openai_default_creativity == 0.75 ? 'selected' : '' }}>
                                        {{ __('Good') }}</option>
                                    <option value="1"
                                        {{ $setting->openai_default_creativity == 1 ? 'selected' : '' }}>
                                        {{ __('Premium') }}</option>
                                </select>
                            </div>

                            <div class="mb-3 col-xs-12 col-md-6">
                                <div class="form-label">{{ __('Tone of Voice') }}</div>
                                <select class="form-select" id="tone_of_voice" name="tone_of_voice" required>
                                    <option value="Professional"
                                        {{ $setting->openai_default_tone_of_voice == 'Professional' ? 'selected' : null }}>
                                        {{ __('Professional') }}</option>
                                    <option value="Funny"
                                        {{ $setting->opena_default_tone_of_voice == 'Funny' ? 'selected' : null }}>
                                        {{ __('Funny') }}</option>
                                    <option value="Casual"
                                        {{ $setting->openai_default_tone_of_voice == 'Casual' ? 'selected' : null }}>
                                        {{ __('Casual') }}</option>
                                    <option value="Excited"
                                        {{ $setting->openai_default_tone_of_voice == 'Excited' ? 'selected' : null }}>
                                        {{ __('Excited') }}</option>
                                    <option value="Witty"
                                        {{ $setting->openai_default_tone_of_voice == 'Witty' ? 'selected' : null }}>
                                        {{ __('Witty') }}</option>
                                    <option value="Sarcastic"
                                        {{ $setting->openai_default_tone_of_voice == 'Sarcastic' ? 'selected' : null }}>
                                        {{ __('Sarcastic') }}</option>
                                    <option value="Feminine"
                                        {{ $setting->openai_default_tone_of_voice == 'Feminine' ? 'selected' : null }}>
                                        {{ __('Feminine') }}</option>
                                    <option value="Masculine"
                                        {{ $setting->openai_default_tone_of_voice == 'Masculine' ? 'selected' : null }}>
                                        {{ __('Masculine') }}</option>
                                    <option value="Bold"
                                        {{ $setting->openai_default_tone_of_voice == 'Bold' ? 'selected' : null }}>
                                        {{ __('Bold') }}</option>
                                    <option value="Dramatic"
                                        {{ $setting->openai_default_tone_of_voice == 'Dramatic' ? 'selected' : null }}>
                                        {{ __('Dramatic') }}</option>
                                    <option value="Grumpy"
                                        {{ $setting->openai_default_tone_of_voice == 'Grumpy' ? 'selected' : null }}>
                                        {{ __('Grumpy') }}</option>
                                    <option value="Secretive"
                                        {{ $setting->openai_default_tone_of_voice == 'Secretive' ? 'selected' : null }}>
                                        {{ __('Secretive') }}</option>
                                </select>
                            </div>
                        @endif

                        <div class="col-xs-12 mt-[10px]">
                            <button id="openai_generator_button"
                                class="btn btn-primary w-100 py-[0.75em] flex items-center group" type="submit">
                                <span
                                    class="hidden group-[.lqd-form-submitting]:inline-flex">{{ __('Please wait...') }}</span>
                                <span class="group-[.lqd-form-submitting]:hidden">{{ __('Generate') }}</span>
                            </button>
                        </div>

                    </form>
                </div>

                <div class="col-sm-6 col-lg-7 lg:pl-16 lg:border-l lg:border-solid border-t-0 border-r-0 border-b-0 border-[var(--tblr-border-color)] [&_.tox-edit-area__iframe]:!bg-transparent"
                    id="workbook_textarea">
                    <div class="row text-[13px] items-center">
                        <div class="col flex items-center">
                            @if ($openai->type != 'code')
                                <div class="flex rtl:flex-row-reverse">
                                    <button
                                        class="bg-transparent p-1 inline-flex items-center justify-center rounded-sm w-[30px] h-[30px] border-0 text-[13px] hover:!bg-[var(--lqd-faded-out)] transition-colors"
                                        id="workbook_undo" title="{{ __('Undo') }}">
                                        <svg width="16" height="17" viewBox="0 0 16 17" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M4.75342 12.7378H10.0868C11.9268 12.7378 13.4201 11.2445 13.4201 9.4045C13.4201 7.5645 11.9268 6.07117 10.0868 6.07117H2.75342"
                                                stroke="var(--lqd-heading-color)" stroke-width="1.25"
                                                stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M4.28674 7.7378L2.58008 6.03113L4.28674 4.32446"
                                                stroke="var(--lqd-heading-color)" stroke-width="1.25"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <span class="sr-only">{{ __('Undo') }}</span>
                                    </button>
                                    <button
                                        class="bg-transparent p-1 inline-flex items-center justify-center rounded-sm w-[30px] h-[30px] border-0 text-[13px] hover:!bg-[var(--lqd-faded-out)] transition-colors"
                                        id="workbook_redo" title="{{ __('Redo') }}">
                                        <svg width="16" height="17" viewBox="0 0 16 17" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M11.2467 12.7378H5.91341C4.07341 12.7378 2.58008 11.2445 2.58008 9.4045C2.58008 7.5645 4.07341 6.07117 5.91341 6.07117H13.2467"
                                                stroke="var(--lqd-heading-color)" stroke-width="1.25"
                                                stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M11.7134 7.7378L13.42 6.03113L11.7134 4.32446"
                                                stroke="var(--lqd-heading-color)" stroke-width="1.25"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <span class="sr-only">{{ __('Redo') }}</span>
                                    </button>
                                </div>
                            @endif
                            <button
                                class="bg-transparent p-1 inline-flex items-center justify-center rounded-sm w-[30px] h-[30px] border-0 text-[13px] hover:!bg-[var(--lqd-faded-out)] transition-colors"
                                id="workbook_copy" title="{{ __('Copy to clipboard') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 96 960 960"
                                    fill="var(--lqd-heading-color)" width="20">
                                    <path
                                        d="M180 975q-24 0-42-18t-18-42V312h60v603h474v60H180Zm120-120q-24 0-42-18t-18-42V235q0-24 18-42t42-18h440q24 0 42 18t18 42v560q0 24-18 42t-42 18H300Zm0-60h440V235H300v560Zm0 0V235v560Z" />
                                </svg>
                                <span class="sr-only">{{ __('Copy to clipboard') }}</span>
                            </button>
                            @if ($openai->type != 'code')
                                <div class="relative">
                                    <button
                                        class="bg-transparent p-1 inline-flex items-center justify-center rounded-sm w-[30px] h-[30px] border-0 text-[13px] hover:!bg-[var(--lqd-faded-out)] transition-colors"
                                        title="{{ __('Download') }}" data-bs-toggle="dropdown" tabindex="-1">
                                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M1.01025 10.7186V12.3124C1.01025 12.7351 1.17817 13.1404 1.47705 13.4393C1.77594 13.7382 2.18132 13.9061 2.604 13.9061H12.1665C12.5892 13.9061 12.9946 13.7382 13.2935 13.4393C13.5923 13.1404 13.7603 12.7351 13.7603 12.3124V10.7186M10.5728 7.53113L7.38525 10.7186M7.38525 10.7186L4.19775 7.53113M7.38525 10.7186V1.15613"
                                                stroke="var(--lqd-heading-color)" stroke-width="1.25"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <span class="sr-only">{{ __('Download') }}</span>
                                    </button>
                                    <div
                                        class="dropdown-menu dropdown-menu-end text-center whitespace-nowrap p-0 [--tblr-dropdown-min-width:150px]">
                                        <div class="flex flex-col p-1 gap-1">
                                            <button
                                                class="workbook_download flex items-center gap-1 p-2 border-none rounded-md bg-[transparent] text-[12px] font-medium text-heading hover:bg-slate-100 dark:hover:bg-zinc-900"
                                                data-doc-type="doc" data-doc-name="{{ $openai->title }}">
                                                <svg class="shrink-0" xmlns="http://www.w3.org/2000/svg" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="1.5"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path d="M4 18h9v-12l-5 2v5l-4 2v-8l9 -4l7 2v13l-7 3z"></path>
                                                </svg>
                                                MS Word
                                            </button>
                                            <button
                                                class="workbook_download flex items-center gap-1 p-2 border-none rounded-md bg-[transparent] text-[12px] font-medium text-heading hover:bg-slate-100 dark:hover:bg-zinc-900"
                                                data-doc-type="html" data-doc-name="{{ $openai->title }}">
                                                <svg class="shrink-0" xmlns="http://www.w3.org/2000/svg" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="1.5"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path d="M20 4l-2 14.5l-6 2l-6 -2l-2 -14.5z"></path>
                                                    <path d="M15.5 8h-7l.5 4h6l-.5 3.5l-2.5 .75l-2.5 -.75l-.1 -.5"></path>
                                                </svg>
                                                HTML
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <a href="javascript:void(0);"
                                class="bg-transparent -mr-1 p-1 inline-flex items-center justify-center rounded-sm w-[30px] h-[30px] border-0 text-[13px] hover:!bg-[var(--lqd-faded-out)] transition-colors"
                                id="workbook_delete" title="{{ __('Delete') }}">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <mask id="mask0_1_7315" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
                                        y="0" width="20" height="20">
                                        <rect x="0.885254" width="19.0623" height="19.0623" fill="#D9D9D9" />
                                    </mask>
                                    <g mask="url(#mask0_1_7315)">
                                        <path
                                            d="M6.44519 10.3254H14.3878V8.73687H6.44519V10.3254ZM10.4165 17.4738C9.31778 17.4738 8.28524 17.2653 7.31888 16.8483C6.35253 16.4313 5.51193 15.8654 4.7971 15.1505C4.08226 14.4357 3.51635 13.5951 3.09936 12.6288C2.68237 11.6624 2.47388 10.6299 2.47388 9.53113C2.47388 8.4324 2.68237 7.39986 3.09936 6.43351C3.51635 5.46715 4.08226 4.62656 4.7971 3.91172C5.51193 3.19688 6.35253 2.63097 7.31888 2.21398C8.28524 1.797 9.31778 1.5885 10.4165 1.5885C11.5152 1.5885 12.5478 1.797 13.5141 2.21398C14.4805 2.63097 15.3211 3.19688 16.0359 3.91172C16.7508 4.62656 17.3167 5.46715 17.7337 6.43351C18.1506 7.39986 18.3591 8.4324 18.3591 9.53113C18.3591 10.6299 18.1506 11.6624 17.7337 12.6288C17.3167 13.5951 16.7508 14.4357 16.0359 15.1505C15.3211 15.8654 14.4805 16.4313 13.5141 16.8483C12.5478 17.2653 11.5152 17.4738 10.4165 17.4738ZM10.4165 15.8852C12.1904 15.8852 13.6928 15.2697 14.924 14.0386C16.1551 12.8075 16.7706 11.305 16.7706 9.53113C16.7706 7.75728 16.1551 6.2548 14.924 5.02369C13.6928 3.79258 12.1904 3.17703 10.4165 3.17703C8.64265 3.17703 7.14017 3.79258 5.90907 5.02369C4.67796 6.2548 4.0624 7.75728 4.0624 9.53113C4.0624 11.305 4.67796 12.8075 5.90907 14.0386C7.14017 15.2697 8.64265 15.8852 10.4165 15.8852Z"
                                            fill="#CE3A3A" />
                                    </g>
                                </svg>
                                <span class="sr-only">{{ __('Delete') }}</span>
                            </a>

                        </div>
                        <div id="savedDiv" class="col items-end text-end hidden">
                            <div class="outer-div">
                                <svg width="17" height="17" viewBox="0 0 17 17" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M15.1718 11.1436C15.1718 13.678 13.6779 15.1719 11.1435 15.1719H5.63125C3.09046 15.1719 1.59375 13.678 1.59375 11.1436V5.61862C1.59375 3.08775 2.5245 1.59387 5.05963 1.59387H6.47629C6.98488 1.59458 7.46371 1.83329 7.76829 2.24058L8.415 3.1005C8.721 3.50708 9.19983 3.7465 9.70842 3.74721H11.713C14.2538 3.74721 15.1916 5.04062 15.1916 7.62675L15.1718 11.1436Z"
                                        stroke="#20725E" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M5.29932 10.2447H11.4866" stroke="#20725E" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="inner-div">
                                    <span>Saved to </span>
                                    <a href="{{ route('dashboard.user.openai.documents.all') }}"
                                        style="color: #20725E; text-decoration: underline;cursor: pointer;">Documents</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($openai->type == 'code')
                        <div
                            class="min-h-full border-solid border-t border-r-0 border-b-0 border-l-0 border-[var(--tblr-border-color)] pt-[30px] mt-[15px]">
                            <pre id="code-pre" class="line-numbers min-h-full [direction:ltr]"><code id="code-output">...</code></pre>
                        </div>
                    @else
                        <div
                            class="border-solid border-t border-r-0 border-b-0 border-l-0 border-[var(--tblr-border-color)] pt-[30px] mt-[15px]">
                            <form class="workbook-form"
                                action="{{ url('/dashboard/user/openai/documents/generate-pdf') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="mb-[20px]">
                                    <input type="text" class="form-control rounded-md"
                                        placeholder="{{ __('Untitled Document...') }}">
                                </div>
                                <div class="mb-[20px]">
                                    <textarea class="form-control tinymce" name="content" id="default" rows="25"></textarea>
                                </div>

                                <div id="qr_generator" style="display: none;">

                                    <!--- Step 1 , Upload images Tab --->
                                    <section id="step1Field">
                                        <div class="row">
                                            <input type="hidden" id="template_id" name="template_id">
                                            <input type="hidden" id="obituary_id" name="obituary_id">
                                            <input type="hidden" id="obituary_name" name="obituary_name">
                                            <input type="hidden" id="obituary_dob" name="obituary_dob">

                                            <div class="container mb-5 mt-5 text-center">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h4>Upload the cover image</h4>
                                                    </div>
                                                    <div class="col-md-12 mt-3">
                                                        <div class="verify-sub-box">
                                                            <div class="file-loading">
                                                                <input id="coverfileupload" type="file"
                                                                    name="profile_image" accept=".jpg,.gif,.png">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- <section class="bg-diffrent mb-3">
                                                <div class="container text-center">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <h4>Upload images that tell the story</h4>
                                                        </div>
                                                        <div class="col-md-12 mt-3">
                                                            <div class="verify-sub-box">
                                                                <div class="file-loading">
                                                                    <input id="multiplefileupload" type="file" name="images[]" accept=".jpg,.gif,.png" multiple>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section> --}}

                                            <div class="col-xs-12 mt-[10px] text-center m-auto w-50">
                                                <button id="pdf_generator_button"
                                                    class="button btn btn-primary mt-5 w-100 py-[0.75em] flex items-center group"
                                                    type="submit">
                                                    <span id="pdf_generator_button_waiting"
                                                        class="hidden group-[.lqd-form-submitting]:inline-flex">Please
                                                        wait...</span>
                                                    <span id="pdf_generator_button_generate"
                                                        class="group-[.lqd-form-submitting]:hidden">Generate</span>
                                                </button>
                                            </div>
                                        </div>
                                    </section>

                                    <!--- Step 2 , QrCode Tab --->
                                    <section id="step2Field" style="display: none;">

                                        <div class="text-left my-3">
                                            <p id="qrCodeContent" style="display: flex; justify-content: center; align-item:center;"></p>

                                            <button type="button" class="button btn btn-primary btn-block text-white" id="prev">&larr;Previous</button>
                                            <a href="#" type="button" class="button btn btn-info btn-block text-white" id="print">Print</a>
                                        </div>
                                    </section>

                                </div>
                            </form>

                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    @if ($setting->hosting_type != 'high')
        <input type="hidden" id="guest_id" value="{{ $apiUrl }}">
        <input type="hidden" id="guest_event_id" value="{{ $apikeyPart1 }}">
        <input type="hidden" id="guest_look_id" value="{{ $apikeyPart2 }}">
        <input type="hidden" id="guest_product_id" value="{{ $apikeyPart3 }}">
    @endif
@endsection
@section('script')
    <script src="/assets/libs/tinymce/tinymce.min.js" defer></script>
    <script src="/assets/js/panel/openai_generator_workbook.js"></script>
    @if ($setting->hosting_type != 'high')
        <script src="/assets/js/panel/openai_generator_workbook_low.js"></script>
    @endif
    @if ($openai->type == 'code')
        <link rel="stylesheet" href="/assets/libs/prism/prism.css">
        <script src="/assets/libs/prism/prism.js"></script>
    @endif
    <script src="{{ asset('assets/js/source.js') }}"></script>
    <script src="https://www.cssscript.com/demo/qr-code-generator-logo-title/easy.qrcode.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.8/js/fileinput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.8/js/plugins/sortable.min.js"></script>

    <script>
        const stream_type = '{!! $settings_two->openai_default_stream_server !!}';
        const openai_model = '{{ $setting->openai_default_model }}';

        function sendOpenaiGeneratorForm(ev) {
            "use strict";
            $('#savedDiv').addClass('hidden');

            tinyMCE?.activeEditor?.setContent('');

            ev?.preventDefault();
            ev?.stopPropagation();
            const submitBtn = document.getElementById("openai_generator_button");
            const editArea = document.querySelector('.tox-edit-area');
            const typingTemplate = document.querySelector('#typing-template').content.cloneNode(true);
            const typingEl = typingTemplate.firstElementChild;
            document.querySelector('#app-loading-indicator')?.classList?.remove('opacity-0');
            submitBtn.classList.add('lqd-form-submitting');
            submitBtn.disabled = true;

            if (editArea) {
                if (!editArea.querySelector('.lqd-typing')) {
                    editArea.appendChild(typingEl);
                } else {
                    editArea.querySelector('.lqd-typing')?.classList?.remove('lqd-is-hidden');
                }
            }

            var formData = new FormData();
            formData.append('post_type', '{{ $openai->slug }}');
            formData.append('openai_id', {{ $openai->id }});
            @if ($openai->type == 'text')
                formData.append('maximum_length', $("#maximum_length").val());
                formData.append('number_of_results', $("#number_of_results").val());
                formData.append('creativity', $("#creativity").val());
                formData.append('tone_of_voice', $("#tone_of_voice").val());
                formData.append('language', $("#language").val());
            @endif

            @foreach (json_decode($openai->questions) as $question)
                formData.append('{{ $question->name }}', $("{{ '#' . $question->name }}").val());
            @endforeach

            $.ajax({
                type: "post",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                },
                url: "/dashboard/user/openai/generate",
                data: formData,
                contentType: false,
                processData: false,
                success: function(data) {
                    @if ($openai->type == 'code')
                        // because data changing in the dom can't cache codeOutput
                        // const codeOutput = $("#code-output");
                        toastr.success('Generated Successfully!');
                        // if ( $("#code-output").length ) {
                        $("#workbook_textarea").html(data.html);
                        window.codeRaw = $("#code-output").text();
                        $("#code-output").addClass(`language-${$('#code_lang').val() || 'javascript'}`);
                        Prism.highlightElement($("#code-output")[0]);
                        // } else {
                        //     tinymce.activeEditor.destroy();
                        //     $("#workbook_textarea").html(data.html);
                        //     getResult();
                        // }
                        submitBtn.classList.remove('lqd-form-submitting');
                        document.querySelector('#app-loading-indicator')?.classList?.add('opacity-0');
                        document.querySelector('#workbook_regenerate')?.classList?.remove('hidden');
                        submitBtn.disabled = false;
                    @else
                        const typingEl = document.querySelector('.tox-edit-area > .lqd-typing');
                        @if ($setting->hosting_type == 'high')
                            // $("#workbook_textarea").html(data.html);
                            // if ( localStorage.getItem( "tablerTheme" ) === 'dark' ) {
                            //     tinymceOptions.skin = 'oxide-dark';
                            //     tinymceOptions.content_css = 'dark';
                            // }
                            // tinyMCE.init( tinymceOptions );

                            let responseText = '';
                            const message_id = data.message_id;
                            const eventSource = new EventSource("/dashboard/user/openai/generate?message_id=" +
                                message_id + "&maximum_length=" + data.maximum_length +
                                "&number_of_results=" + data.number_of_results + "&creativity=" + data
                                .creativity);
                            eventSource.onmessage = function(e) {
                                let txt = e.data;
                                typingEl.classList.add('lqd-is-hidden');
                                if (txt === '[DONE]') {
                                    //This is the area when the chat ends.
                                    eventSource.close();
                                    submitBtn.classList.remove('lqd-form-submitting');
                                    document.querySelector('#app-loading-indicator')?.classList?.add(
                                        'opacity-0');
                                    document.querySelector('#workbook_regenerate')?.classList?.remove(
                                        'hidden');
                                    submitBtn.disabled = false;
                                }
                                if (txt && txt !== '[DONE]') {
                                    responseText += txt.split("/**")[0];
                                    tinyMCE.activeEditor.setContent(responseText, {
                                        format: 'raw'
                                    });
                                }
                            };
                        @else
                            // $("#workbook_textarea").html(data.html);

                            const message_no = data.message_id;
                            const creativity = data.creativity;
                            const maximum_length = parseInt(data.maximum_length);
                            const number_of_results = data.number_of_results;
                            const prompt = data.inputPrompt;

                            $("#obituary_name").val($("#full-name").val());
                            $("#obituary_dob").val($("#date-of-birth").val() + '-' + $("#date-of-death").val());

                            return generate(message_no, creativity, maximum_length, number_of_results, prompt);
                        @endif
                    @endif

                    setTimeout(function() {
                        $('#savedDiv').removeClass('hidden');
                    }, 1000);
                },
                error: function(data) {
                    if (data.responseJSON.errors) {
                        $.each(data.responseJSON.errors, function(index, value) {
                            toastr.error(value);
                        });
                    } else if (data.responseJSON.message) {
                        toastr.error(data.responseJSON.message);
                    }
                    submitBtn.classList.remove('lqd-form-submitting');
                    document.querySelector('#app-loading-indicator')?.classList?.add('opacity-0');
                    document.querySelector('#workbook_regenerate')?.classList?.add('hidden');
                    submitBtn.disabled = false;
                }
            });
            return false;
        }

        const deleteButton = document.getElementById("workbook_delete");
        deleteButton.addEventListener("click", clearWorkbookContent);

        function clearWorkbookContent() {
            const editor = tinyMCE.activeEditor;
            if (editor) {
                editor.setContent("");
            }
        }

        // ----------multiplefile-upload---------
        $("#coverfileupload").fileinput({
            'theme': 'fa',
            'uploadUrl': '#',
            showRemove: false,
            showUpload: false,
            showZoom: false,
            showCaption: false,
            browseClass: "btn btn-danger",
            browseLabel: "",
            browseIcon: "<i class='fa fa-plus'></i>",
            overwriteInitial: false,
            initialPreviewAsData: true,
            fileActionSettings: {
                showUpload: false,
                showZoom: false,
                removeIcon: "<i class='fa fa-times'></i>",
            }
        });

        $("#multiplefileupload").fileinput({
            'theme': 'fa',
            'uploadUrl': '#',
            showRemove: false,
            showUpload: false,
            showZoom: false,
            showCaption: false,
            browseClass: "btn btn-danger",
            browseLabel: "",
            browseIcon: "<i class='fa fa-plus'></i>",
            overwriteInitial: false,
            initialPreviewAsData: true,
            fileActionSettings: {
                showUpload: false,
                showZoom: false,
                removeIcon: "<i class='fa fa-times'></i>",
            }
        });

        $(document).ready(function() {
            $(".workbook-form").on('submit', (function(e) {
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    type: "POST",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        $("#pdf_generator_button").attr('disabled', true)
                        $("#pdf_generator_button_generate").hide()
                        $("#pdf_generator_button_waiting").show()
                    },
                    success: function(response) {
                        if (response.status) {
                            $("#step1Field").hide()
                            $("#qrCodeContent").html('')

                            new QRCode(document.getElementById("qrCodeContent"), {
                                text: `${window.location.origin}/scan-book/${response.obituary.qrCodeId}`,
                                logo: 'https://obu.harrisarshad.com/upload/images/logo/640e-2x-funeralize-logo.png',
                                width: 220,
                                height: 220,
                            });

                            $("#print").attr('href', `${window.location.origin}/preview/${response.obituary.qrCodeId}/print`)
                            $("#step2Field").show()

                            $("#pdf_generator_button").removeAttr('disabled')
                            $("#pdf_generator_button_generate").show()
                            $("#pdf_generator_button_waiting").hide()

                        }
                    },
                    error: function(e) {
                        console.log(e);
                    }
                });
            }));
        });

        $("#template_id").val(getCookie('template_id'));

        function getCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }

        $(document).ready(function() {
            var child = 1;
            var length = $("section").length - 1;
            $("#submit").addClass("disabled");

            $("section").not("section:nth-of-type(1)").hide();

            $(".button").click(function() {

                var id = $(this).attr("id");
                if (id == "next") {
                    $("#prev").removeClass("disabled");
                    if (child >= length) {
                        $(this).addClass("disabled");
                        $('#submit').removeClass("disabled");
                    }
                    if (child <= length) {
                        child++;
                    }
                } else if (id == "prev") {
                    $("#next").removeClass("disabled");
                    if (child <= 2) {
                        $(this).removeClass("disabled");
                    }
                    if (child > 1) {
                        child--;
                    }
                }

                var currentSection = $("section:nth-of-type(" + child + ")");
                currentSection.fadeIn();
                currentSection.css('transform', 'translateX(0)');
                currentSection.prevAll('section').css('transform', 'translateX(-100px)');
                $('section').not(currentSection).hide();
            });

        });
    </script>

@endsection
