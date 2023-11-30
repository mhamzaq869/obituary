<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>PDF.js viewer</title>

    <!-- This snippet is used in production (included from viewer.html) -->
    <style>
        body{
            margin: 0  !important;
            padding: 0 !important;
            background-color: #525659;
        }

        .pdfobject-container { height: 99vh;}
    </style>
</head>

<body>
    <div id="example1"></div>

    <script src="https://cdn.jsdelivr.net/npm/pdfobject@2.2.12/pdfobject.min.js"></script>
    <script>PDFObject.embed("{{asset($template->filePath)}}", "#example1",  {width: "100%"});</script>
</body>

</html>
