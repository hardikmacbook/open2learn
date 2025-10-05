<?php
session_start();

// Define admin credentials (change as needed)
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin@gmail.com');

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . basename(__FILE__));
    exit;
}

// Handle login submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    if ($user === ADMIN_USER && $pass === ADMIN_PASS) {
        $_SESSION['is_admin'] = true;
        header('Location: ' . basename(__FILE__));
        exit;
    } else {
        $login_error = "Invalid username or password.";
    }
}

// If not logged in, show login form and exit
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Admin - Login</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" />
    </head>
    <body class="bg-gray-50 flex items-center justify-center min-h-screen">
        <form method="post" class="bg-white p-8 rounded shadow-md w-full max-w-sm">
            <h2 class="text-2xl font-semibold mb-6 text-center">Admin Login</h2>
            <?php if (!empty($login_error)): ?>
                <p class="mb-4 text-red-600"><?= htmlspecialchars($login_error) ?></p>
            <?php endif; ?>
            <label class="block mb-2 text-gray-700" for="username">Username</label>
            <input type="text" id="username" name="username" class="mb-4 w-full border rounded px-3 py-2" required autofocus />

            <label class="block mb-2 text-gray-700" for="password">Password</label>
            <input type="password" id="password" name="password" class="mb-6 w-full border rounded px-3 py-2" required />

            <button type="submit" name="login" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Login</button>

             <a href="index.php" class="mt-3 block text-center w-full bg-gray-300 text-gray-700 py-2 rounded hover:bg-gray-400">Back to Site</a>
        </form>
    </body>
    </html>
    <?php
    exit;
}

// --------- Admin Panel Code ---------
require_once __DIR__ . '/includes/db_connect.php';

// Create tables if they don't exist
$conn->query("CREATE TABLE IF NOT EXISTS features (
  id INT AUTO_INCREMENT PRIMARY KEY,
  icon_class VARCHAR(100) DEFAULT 'fas fa-check-circle',
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$conn->query("CREATE TABLE IF NOT EXISTS reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  review TEXT NOT NULL,
  rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$conn->query("CREATE TABLE IF NOT EXISTS faculty (
  id INT AUTO_INCREMENT PRIMARY KEY,
  image VARCHAR(255),
  name VARCHAR(255) NOT NULL,
  designation VARCHAR(255) NOT NULL,
  degree VARCHAR(255) NOT NULL,
  specialization TEXT NOT NULL,
  experience VARCHAR(50) NOT NULL,
  email VARCHAR(255) NOT NULL,
  department VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Handle actions
function redirect_home() {
  header('Location: admin.php');
  exit;
}

// Handle file upload
function handle_file_upload($file) {
  if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
    return null;
  }
  
  $upload_dir = __DIR__ . '/uploads/faculty/';
  if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
  }
  
  $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
  $filename = uniqid() . '.' . $ext;
  $filepath = $upload_dir . $filename;
  
  if (move_uploaded_file($file['tmp_name'], $filepath)) {
    return 'uploads/faculty/' . $filename;
  }
  return null;
}

// Create/Update Feature
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['entity']) && $_POST['entity'] === 'feature') {
  $icon_class = trim($_POST['icon_class'] ?? 'fas fa-check-circle');
  $title = trim($_POST['title'] ?? '');
  $description = trim($_POST['description'] ?? '');

  if (isset($_POST['action']) && $_POST['action'] === 'create') {
    $stmt = $conn->prepare('INSERT INTO features(icon_class, title, description) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $icon_class, $title, $description);
    $stmt->execute();
    $stmt->close();
    redirect_home();
  }

  if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = intval($_POST['id'] ?? 0);
    $stmt = $conn->prepare('UPDATE features SET icon_class = ?, title = ?, description = ? WHERE id = ?');
    $stmt->bind_param('sssi', $icon_class, $title, $description, $id);
    $stmt->execute();
    $stmt->close();
    redirect_home();
  }
}

// Delete Feature
if (isset($_GET['delete_feature'])) {
  $id = intval($_GET['delete_feature']);
  $conn->query('DELETE FROM features WHERE id=' . $id);
  redirect_home();
}

// Create/Update Review
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['entity']) && $_POST['entity'] === 'review') {
  $name = trim($_POST['name'] ?? '');
  $review = trim($_POST['review'] ?? '');
  $rating = intval($_POST['rating'] ?? 5);
  if ($rating < 1) $rating = 1;
  if ($rating > 5) $rating = 5;

  if (isset($_POST['action']) && $_POST['action'] === 'create') {
    $stmt = $conn->prepare('INSERT INTO reviews(name, review, rating) VALUES (?, ?, ?)');
    $stmt->bind_param('ssi', $name, $review, $rating);
    $stmt->execute();
    $stmt->close();
    redirect_home();
  }

  if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = intval($_POST['id'] ?? 0);
    $stmt = $conn->prepare('UPDATE reviews SET name = ?, review = ?, rating = ? WHERE id = ?');
    $stmt->bind_param('ssii', $name, $review, $rating, $id);
    $stmt->execute();
    $stmt->close();
    redirect_home();
  }
}

// Delete Review
if (isset($_GET['delete_review'])) {
  $id = intval($_GET['delete_review']);
  $conn->query('DELETE FROM reviews WHERE id=' . $id);
  redirect_home();
}

// Create/Update Faculty
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['entity']) && $_POST['entity'] === 'faculty') {
  $name = trim($_POST['name'] ?? '');
  $designation = trim($_POST['designation'] ?? '');
  $degree = trim($_POST['degree'] ?? '');
  $specialization = trim($_POST['specialization'] ?? '');
  $experience = trim($_POST['experience'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $department = trim($_POST['department'] ?? '');
  
  $image = handle_file_upload($_FILES['image'] ?? null);

  if (isset($_POST['action']) && $_POST['action'] === 'create') {
    $stmt = $conn->prepare('INSERT INTO faculty(image, name, designation, degree, specialization, experience, email, department) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('ssssssss', $image, $name, $designation, $degree, $specialization, $experience, $email, $department);
    $stmt->execute();
    $stmt->close();
    redirect_home();
  }

  if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = intval($_POST['id'] ?? 0);
    if ($image) {
      $stmt = $conn->prepare('UPDATE faculty SET image = ?, name = ?, designation = ?, degree = ?, specialization = ?, experience = ?, email = ?, department = ? WHERE id = ?');
      $stmt->bind_param('ssssssssi', $image, $name, $designation, $degree, $specialization, $experience, $email, $department, $id);
    } else {
      $stmt = $conn->prepare('UPDATE faculty SET name = ?, designation = ?, degree = ?, specialization = ?, experience = ?, email = ?, department = ? WHERE id = ?');
      $stmt->bind_param('sssssssi', $name, $designation, $degree, $specialization, $experience, $email, $department, $id);
    }
    $stmt->execute();
    $stmt->close();
    redirect_home();
  }
}

// Delete Faculty
if (isset($_GET['delete_faculty'])) {
  $id = intval($_GET['delete_faculty']);
  $conn->query('DELETE FROM faculty WHERE id=' . $id);
  redirect_home();
}

// Fetch for listing and editing
$features = $conn->query('SELECT * FROM features ORDER BY id ASC');
$reviews = $conn->query('SELECT * FROM reviews ORDER BY id ASC');
$faculties = $conn->query('SELECT * FROM faculty ORDER BY id ASC');

$editFeature = null;
if (isset($_GET['edit_feature'])) {
  $id = intval($_GET['edit_feature']);
  $res = $conn->query('SELECT * FROM features WHERE id=' . $id . ' LIMIT 1');
  $editFeature = $res && $res->num_rows ? $res->fetch_assoc() : null;
}

$editReview = null;
if (isset($_GET['edit_review'])) {
  $id = intval($_GET['edit_review']);
  $res = $conn->query('SELECT * FROM reviews WHERE id=' . $id . ' LIMIT 1');
  $editReview = $res && $res->num_rows ? $res->fetch_assoc() : null;
}

$editFaculty = null;
if (isset($_GET['edit_faculty'])) {
  $id = intval($_GET['edit_faculty']);
  $res = $conn->query('SELECT * FROM faculty WHERE id=' . $id . ' LIMIT 1');
  $editFaculty = $res && $res->num_rows ? $res->fetch_assoc() : null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Open2Learn - Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" />
</head>
<body class="bg-gray-50">
  <div class="max-w-7xl mx-auto px-6 py-10">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold">Admin Panel</h1>
      <div class="flex gap-4">
        <a href="index.php" class="text-blue-600 hover:underline">View Site</a>
        <a href="?logout" class="text-red-600 hover:underline">Logout</a>
      </div>
    </div>
    

    <!-- Faculty Section -->
    <section class="mb-12">
      <h2 class="text-2xl font-semibold mb-4">Faculty Management</h2>
      
      <form method="post" enctype="multipart/form-data" class="bg-white p-4 rounded-lg border mb-6" autocomplete="off">
        <input type="hidden" name="entity" value="faculty" />
        <?php if ($editFaculty): ?>
          <input type="hidden" name="action" value="update" />
          <input type="hidden" name="id" value="<?= (int)$editFaculty['id'] ?>" />
        <?php else: ?>
          <input type="hidden" name="action" value="create" />
        <?php endif; ?>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm text-gray-600 mb-1">Faculty Image</label>
            <input type="file" name="image" accept="image/*" class="w-full border rounded px-3 py-2" />
            <?php if ($editFaculty && $editFaculty['image']): ?>
              <p class="text-xs text-gray-500 mt-1">Current: <?= htmlspecialchars($editFaculty['image']) ?></p>
            <?php endif; ?>
          </div>
          
          <div>
            <label class="block text-sm text-gray-600 mb-1">Name *</label>
            <input name="name" class="w-full border rounded px-3 py-2" value="<?= htmlspecialchars($editFaculty['name'] ?? '') ?>" required />
          </div>
          
          <div>
            <label class="block text-sm text-gray-600 mb-1">Designation *</label>
            <input name="designation" class="w-full border rounded px-3 py-2" value="<?= htmlspecialchars($editFaculty['designation'] ?? '') ?>" required />
          </div>
          
          <div>
            <label class="block text-sm text-gray-600 mb-1">Degree *</label>
            <input name="degree" class="w-full border rounded px-3 py-2" value="<?= htmlspecialchars($editFaculty['degree'] ?? '') ?>" required />
          </div>
          
          <div>
            <label class="block text-sm text-gray-600 mb-1">Experience *</label>
            <input name="experience" class="w-full border rounded px-3 py-2" value="<?= htmlspecialchars($editFaculty['experience'] ?? '') ?>" placeholder="e.g., 10 Years" required />
          </div>
          
          <div>
            <label class="block text-sm text-gray-600 mb-1">Email *</label>
            <input type="email" name="email" class="w-full border rounded px-3 py-2" value="<?= htmlspecialchars($editFaculty['email'] ?? '') ?>" required />
          </div>
          
          <div>
            <label class="block text-sm text-gray-600 mb-1">Department *</label>
            <input name="department" class="w-full border rounded px-3 py-2" value="<?= htmlspecialchars($editFaculty['department'] ?? '') ?>" placeholder="e.g., Computer Science" required />
          </div>
          
          <div class="md:col-span-2">
            <label class="block text-sm text-gray-600 mb-1">Specialization *</label>
            <textarea name="specialization" class="w-full border rounded px-3 py-2" rows="2" required><?= htmlspecialchars($editFaculty['specialization'] ?? '') ?></textarea>
          </div>
        </div>
        
        <div class="flex gap-3 mt-4">
          <button class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700" type="submit">
            <?= $editFaculty ? 'Update Faculty' : 'Add Faculty' ?>
          </button>
          <?php if ($editFaculty): ?>
            <a href="admin.php" class="px-6 py-2 rounded border hover:bg-gray-50">Cancel Edit</a>
          <?php endif; ?>
        </div>
      </form>

      <div class="bg-white rounded-lg border overflow-x-auto">
        <table class="min-w-full">
          <thead>
            <tr class="text-left border-b bg-gray-50">
              <th class="p-3">ID</th>
              <th class="p-3">Image</th>
              <th class="p-3">Name</th>
              <th class="p-3">Designation</th>
              <th class="p-3">Degree</th>
              <th class="p-3">Department</th>
              <th class="p-3">Experience</th>
              <th class="p-3">Email</th>
              <th class="p-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($faculties && $faculties->num_rows): while($row = $faculties->fetch_assoc()): ?>
              <tr class="border-b hover:bg-gray-50">
                <td class="p-3"><?= (int)$row['id'] ?></td>
                <td class="p-3">
                  <?php if ($row['image']): ?>
                    <img src="<?= htmlspecialchars($row['image']) ?>" alt="Faculty" class="w-12 h-12 object-cover rounded" />
                  <?php else: ?>
                    <span class="text-gray-400">No Image</span>
                  <?php endif; ?>
                </td>
                <td class="p-3"><?= htmlspecialchars($row['name']) ?></td>
                <td class="p-3"><?= htmlspecialchars($row['designation']) ?></td>
                <td class="p-3"><?= htmlspecialchars($row['degree']) ?></td>
                <td class="p-3"><?= htmlspecialchars($row['department']) ?></td>
                <td class="p-3"><?= htmlspecialchars($row['experience']) ?></td>
                <td class="p-3"><?= htmlspecialchars($row['email']) ?></td>
                <td class="p-3">
                  <div class="flex gap-2">
                    <a href="admin.php?edit_faculty=<?= (int)$row['id'] ?>" class="text-blue-600 hover:underline">Edit</a>
                    <a href="admin.php?delete_faculty=<?= (int)$row['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Delete this faculty member?')">Delete</a>
                  </div>
                </td>
              </tr>
            <?php endwhile; else: ?>
              <tr><td class="p-3 text-gray-500" colspan="9">No faculty members yet. Add one above.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Features Section -->
    <section class="mb-12">
      <h2 class="text-2xl font-semibold mb-4">Why Choose Us (Features)</h2>

      <form method="post" class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-white p-4 rounded-lg border" autocomplete="off">
        <input type="hidden" name="entity" value="feature" />
        <?php if ($editFeature): ?>
          <input type="hidden" name="action" value="update" />
          <input type="hidden" name="id" value="<?= (int)$editFeature['id'] ?>" />
        <?php else: ?>
          <input type="hidden" name="action" value="create" />
        <?php endif; ?>
        <div>
          <label class="block text-sm text-gray-600">Icon Class</label>
          <input name="icon_class" class="w-full border rounded px-3 py-2" value="<?= htmlspecialchars($editFeature['icon_class'] ?? '') ?>" placeholder="fas fa-check-circle" />
        </div>
        <div>
          <label class="block text-sm text-gray-600">Title</label>
          <input name="title" class="w-full border rounded px-3 py-2" value="<?= htmlspecialchars($editFeature['title'] ?? '') ?>" required />
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm text-gray-600">Description</label>
          <input name="description" class="w-full border rounded px-3 py-2" value="<?= htmlspecialchars($editFeature['description'] ?? '') ?>" required />
        </div>
        <div class="md:col-span-4 flex gap-3">
          <button class="bg-blue-600 text-white px-4 py-2 rounded" type="submit">Save</button>
          <?php if ($editFeature): ?>
            <a href="admin.php" class="px-4 py-2 rounded border">Cancel Edit</a>
          <?php endif; ?>
        </div>
      </form>

      <div class="mt-6 bg-white rounded-lg border overflow-x-auto">
        <table class="min-w-full">
          <thead>
            <tr class="text-left border-b">
              <th class="p-3">ID</th>
              <th class="p-3">Icon</th>
              <th class="p-3">Title</th>
              <th class="p-3">Description</th>
              <th class="p-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($features && $features->num_rows): while($row = $features->fetch_assoc()): ?>
              <tr class="border-b">
                <td class="p-3"><?= (int)$row['id'] ?></td>
                <td class="p-3"><span class="inline-flex items-center gap-2"><i class="<?= htmlspecialchars($row['icon_class']) ?>"></i> <code><?= htmlspecialchars($row['icon_class']) ?></code></span></td>
                <td class="p-3"><?= htmlspecialchars($row['title']) ?></td>
                <td class="p-3"><?= htmlspecialchars($row['description']) ?></td>
                <td class="p-3 flex gap-3">
                  <a href="admin.php?edit_feature=<?= (int)$row['id'] ?>" class="text-blue-600">Edit</a>
                  <a href="admin.php?delete_feature=<?= (int)$row['id'] ?>" class="text-red-600" onclick="return confirm('Delete feature?')">Delete</a>
                </td>
              </tr>
            <?php endwhile; else: ?>
              <tr><td class="p-3" colspan="5">No features yet.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Reviews Section -->
    <section>
      <h2 class="text-2xl font-semibold mb-4">Reviews</h2>
      <form method="post" class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-white p-4 rounded-lg border" autocomplete="off">
        <input type="hidden" name="entity" value="review" />
        <?php if ($editReview): ?>
          <input type="hidden" name="action" value="update" />
          <input type="hidden" name="id" value="<?= (int)$editReview['id'] ?>" />
        <?php else: ?>
          <input type="hidden" name="action" value="create" />
        <?php endif; ?>
        <div>
          <label class="block text-sm text-gray-600">Name</label>
          <input name="name" class="w-full border rounded px-3 py-2" value="<?= htmlspecialchars($editReview['name'] ?? '') ?>" required />
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm text-gray-600">Review</label>
          <input name="review" class="w-full border rounded px-3 py-2" value="<?= htmlspecialchars($editReview['review'] ?? '') ?>" required />
        </div>
        <div>
          <label class="block text-sm text-gray-600">Rating (1-5)</label>
          <input type="number" min="1" max="5" name="rating" class="w-full border rounded px-3 py-2" value="<?= htmlspecialchars($editReview['rating'] ?? 5) ?>" required />
        </div>
        <div class="md:col-span-4 flex gap-3">
          <button class="bg-blue-600 text-white px-4 py-2 rounded" type="submit">Save</button>
          <?php if ($editReview): ?>
            <a href="admin.php" class="px-4 py-2 rounded border">Cancel Edit</a>
          <?php endif; ?>
        </div>
      </form>

      <div class="mt-6 bg-white rounded-lg border overflow-x-auto">
        <table class="min-w-full">
          <thead>
            <tr class="text-left border-b">
              <th class="p-3">ID</th>
              <th class="p-3">Name</th>
              <th class="p-3">Review</th>
              <th class="p-3">Rating</th>
              <th class="p-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($reviews && $reviews->num_rows): while($row = $reviews->fetch_assoc()): ?>
              <tr class="border-b">
                <td class="p-3"><?= (int)$row['id'] ?></td>
                <td class="p-3"><?= htmlspecialchars($row['name']) ?></td>
                <td class="p-3"><?= htmlspecialchars($row['review']) ?></td>
                <td class="p-3"><?= (int)$row['rating'] ?></td>
                <td class="p-3 flex gap-3">
                  <a href="admin.php?edit_review=<?= (int)$row['id'] ?>" class="text-blue-600">Edit</a>
                  <a href="admin.php?delete_review=<?= (int)$row['id'] ?>" class="text-red-600" onclick="return confirm('Delete review?')">Delete</a>
                </td>
              </tr>
            <?php endwhile; else: ?>
              <tr><td class="p-3" colspan="5">No reviews yet.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

  </div>
</body>
</html>
