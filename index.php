<?php
include "conn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_btn'])) {
    $filename = $_FILES["choosefile"]["name"];
    $tempfile = $_FILES["choosefile"]["tmp_name"];
    $folder = "files/" . $filename;

    $fileType = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if ($fileType != "pdf") {
        echo "<div class='alert alert-danger text-center'>Only PDF files are allowed!</div>";
    } else {

        $sql = "INSERT INTO files (file) VALUES ('$filename')";
        if (mysqli_query($conn, $sql)) {
            move_uploaded_file($tempfile, $folder);
            header("Location: index.php");
            exit();
        } else {
            echo "<div class='alert alert-danger text-center'>Error uploading file: " . mysqli_error($conn) . "</div>";
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_btn'])) {
    mysqli_query($conn, "DELETE FROM files");
    mysqli_query($conn, "ALTER TABLE files AUTO_INCREMENT = 1");
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Upload & Display</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">

    <form action="index.php" method="post" class="mb-3" enctype="multipart/form-data">
        <input type="file" class="form-control" name="choosefile" required>
        <button type="submit" name="submit_btn" class="btn btn-success mt-2">Upload PDF</button>
        <button type="submit" name="reset_btn" class="btn btn-danger mt-2">Reset Table</button>
    </form>

    <table class="table table-bordered text-center">
        <tr>
            <th>ID</th>
            <th>PDF File</th>
            <th>Action</th>
        </tr>

        <?php
        $sql = "SELECT * FROM files";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                    <td>{$row["id"]}</td>
                    <td>{$row["file"]}</td>
                    <td>
                        <button class='btn btn-primary' onclick='openPDF(\"files/{$row["file"]}\")'>View</button>
                        <a href='delete.php?id={$row["id"]}' class='btn btn-danger'>Delete</a>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No files uploaded yet</td></tr>";
        }
        ?>
    </table>

    <!-- Modal -->
    <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">PDF Viewer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe id="pdfFrame" src="" width="100%" height="500px"></iframe>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openPDF(fileUrl) {
            document.getElementById('pdfFrame').src = fileUrl;
            var pdfModal = new bootstrap.Modal(document.getElementById('pdfModal'));
            pdfModal.show();
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>