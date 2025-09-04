<?php include 'includes/header.php'; ?>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "open2learn";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$faculty_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT * FROM faculty WHERE id = $faculty_id";
$result = $conn->query($sql);
$faculty = $result && $result->num_rows > 0 ? $result->fetch_assoc() : null;

$conn->close();
?>

<div class="max-w-3xl mx-auto px-4 py-16">
     <!-- Optional: Back button -->
    
    <?php if ($faculty): ?>
        <div class="bg-gradient-to-br from-blue-70 via-purple-100 to-indigo-100 rounded-xl shadow-lg p-8 border border-gray-200 flex flex-col items-center mt-10">
            <?php if (!empty($faculty['image'])): ?>
                <img src="<?= htmlspecialchars($faculty['image']) ?>" alt="<?= htmlspecialchars($faculty['name']) ?>" class="faculty-image" style="max-width:240px;max-height:320px;">
            <?php else: ?>
                <div class="image-placeholder" style="height:160px;width:160px;">
                    <i class="fas fa-user-tie"></i>
                </div>
            <?php endif; ?>
            <h2 class="text-3xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($faculty['name']) ?></h2>
            <p class="text-blue-700 font-medium mb-3"><?= htmlspecialchars($faculty['designation']) ?></p>
            <div class="space-y-2 text-gray-700 text-base mb-4 w-full lg:w-2/3 mx-auto">
                <p><span class="font-semibold">Department:</span> <?= htmlspecialchars($faculty['department']) ?></p>
                <p><span class="font-semibold">Qualification:</span> <?= htmlspecialchars($faculty['degree']) ?></p>
                <p><span class="font-semibold">Specialization:</span> <?= htmlspecialchars($faculty['specialization']) ?></p>
                <p><span class="font-semibold">Experience:</span> <?= htmlspecialchars($faculty['experience']) ?></p>
            </div>
            <div class="border-t w-full my-4"></div>
            <div class="text-base text-center">
                <p><span class="font-semibold">Email:</span>
                  <a href="mailto:<?= htmlspecialchars($faculty['email']) ?>" class="text-blue-600 hover:underline"><?= htmlspecialchars($faculty['email']) ?></a>
                </p>
            </div>
        </div>
    <?php else: ?>
        <p class="text-center text-red-500 font-semibold">Faculty not found or invalid ID.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
