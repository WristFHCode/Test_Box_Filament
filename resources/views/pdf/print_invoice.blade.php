<!DOCTYPE html>
<html>
<head>
    <title>Print Invoice</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
</head>
<body>
    <iframe id="pdf-frame" src="{{ $pdfUrl }}"></iframe>

    <script>
        window.onload = function() {
            // Trigger the print dialog after the PDF is loaded
            document.getElementById('pdf-frame').onload = function() {
                window.print();
            };
        };
    </script>
</body>
</html>
