<!DOCTYPE html>
<html lang="en">
<head>
    <title>Testing webcam to take a picture</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <style type="text/css">
        #captureBtn {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Testing webcam to take a picture</h1>
        <form method="POST" action="upload.php">
            <div class="row">
                <div class="col-md-6">
                    <div id="img"></div>
                    <br />
                    <label class="form-label" for="image">Upload Your Photo:</label>
                    <!-- Trigger attachCamera on input click -->
                    <input class="form-control rounded-pill text-dark" id="image" accept="image/jpeg" capture="user" placeholder="Upload photo:" onclick="attachCamera()" />
                    <!-- Display Capture button after camera is attached -->
                    <input type="button" value="Capture" id="captureBtn" name="captureBtn" accept="image/jpeg" onclick="take_snapshot()" />
                    <input type="hidden" name="image" class="image-tag">
                    <div id="livePhoto"></div>
                </div>
                <div class="col-md-6">
                    <div id="results">Your captured image will appear here...</div>
                </div>
                <div class="col-md-12 text-center">
                    <br />
                    <button class="btn btn-success">Submit</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Configure a few settings and attach camera -->
    <script>
        Webcam.set({
            width: 320,
            height: 320,
            image_format: 'jpeg',
            jpeg_quality: 90
        });

        function attachCamera() {
            Webcam.attach('#livePhoto');
            document.getElementById('captureBtn').style.display = 'block';
            document.getElementById('image').style.display = 'none';
        }

        function take_snapshot() {
            Webcam.snap(function (data_uri) {
                $(".image-tag").val(data_uri);
                document.getElementById('results').innerHTML = '<img src="' + data_uri + '"/>';
            });
        }
    </script>
</body>
</html>
