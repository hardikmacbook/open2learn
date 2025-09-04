<?php include 'includes/header.php'; ?>

<style>
  /* Custom Scrollbar for specialization */
  .specialization-scroll {
    max-height: 100px;
    overflow-y: auto;
    padding-right: 8px;
  }

  /* Responsive image: fill width, max height, rounded */
  .faculty-image {
    width: 100%;
    height: 16rem;
    /* 256px (h-64 in Tailwind) */
    max-height: 20rem;
    /* 320px (h-80 in Tailwind) */
    object-fit: cover;
    border-radius: 0.75rem;
    /* Rounded-xl */
    display: block;
    margin: 0 auto 1rem auto;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.07) inset;
    transition: transform 0.3s;
  }

  .faculty-image:hover {
    transform: scale(1.04);
  }

  .image-placeholder {
    height: 160px;
    width: 160px;
    border-radius: 10px;
    background-color: #bfdbfe;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem auto;
  }

  .image-placeholder i {
    font-size: 48px;
    color: #3b82f6;
  }

  .tabs-container {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    /* Enables horizontal scrolling */
    -webkit-overflow-scrolling: touch;
    /* Smooth scrolling on iOS */
    justify-content: center;
    padding-bottom: 0.5rem;
    white-space: nowrap;
    /* Keep buttons in single line */
    scrollbar-width: none;
    /* Firefox: hide scrollbar */
  }

  .tabs-container::-webkit-scrollbar {
    display: none;
    /* Chrome, Safari & Opera: hide scrollbar */
  }

  .tab-btn {
    min-width: 60px;
    background: #fff;
    border-radius: 0.75rem;
    border: 1.5px solid #e5e7eb;
    font-weight: 500;
    font-size: 1rem;
    transition: background .2s, color .2s, border .2s;
    color: #1e293b;
    white-space: nowrap;
    flex-shrink: 0;
    /* Prevent shrinking to keep width */
    padding: 0.3rem 0.6rem;
    cursor: pointer;
  }

  .tab-btn.active,
  .tab-btn:focus,
  .tab-btn:active {
    background: #1E3A8A;
    color: #fff;
    border-color: #1E3A8A;
    outline: none;
  }
</style>

<div class="title-container mt-28 mb-16">

  <div class="text-center mb-16">
    <h2 class="text-4xl font-bold text-gray-900 mb-4">
      Explore <span class="text-[#1E3A8A]">Open<span class="text-[#BFA14A]">2</span>Learn</span> Faculty
    </h2>

    <p class="text-gray-600 max-w-2xl mx-auto">
      Top-class teachers, bringing quality learning to every student.
    </p>
  </div>

  <?php
  $servername = "localhost"; // you can change it to your server name
  $username = "root"; // enter you MySQL User Name
  $password = ""; // enter your hosting panel password
  $dbname = "open2learn"; // your database name

  $conn = new mysqli($servername, $username, $password, $dbname);
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $sql = "SELECT * FROM faculty";
  $result = $conn->query($sql);

  $facultyData = [];
  if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $facultyData[] = $row;
    }
  }
  $conn->close();

  $departments = ["ALL", "BCA", "BBA", "LAB", "LIBRARY"];
  ?>

  <!-- Tabs Section -->
  <div class="tabs-container mb-10">
    <?php foreach ($departments as $dept) { ?>
      <button
        class="tab-btn"
        data-dept="<?= $dept ?>">
        <?= $dept ?>
      </button>
    <?php } ?>
  </div>

  <!-- Faculty Cards List -->
  <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
    <div id="faculty-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">

      <?php
      if ($facultyData) {
        foreach ($facultyData as $faculty) {
      ?>
          <!-- Faculty Card -->
          <div
            class="faculty-card bg-white rounded-xl shadow-lg p-6 border border-gray-200 hover:shadow-xl hover:border-[#1E3A8A] transition-all duration-300 flex flex-col"
            data-dept="<?= strtoupper($faculty['department']) ?>">

            <!-- Image -->
            <?php if (!empty($faculty['image'])): ?>
              <a href="faculty-detail.php?id=<?= $faculty['id'] ?>">
                <img src="<?= htmlspecialchars($faculty['image']) ?>" alt="<?= htmlspecialchars($faculty['name']) ?>" class="faculty-image">
              </a>

            <?php else: ?>
              <div class="image-placeholder">
                <i class="fas fa-user-tie"></i>
              </div>
            <?php endif; ?>

            <!-- Name -->
            <h3 class="text-2xl font-bold text-gray-800 text-center mb-2">
              <?= htmlspecialchars($faculty['name']) ?>
            </h3>

            <!-- Designation -->
            <p class="text-blue-700 text-center font-medium mb-3">
              <?= htmlspecialchars($faculty['designation']) ?>
            </p>

            <!-- Info Section -->
            <div class="space-y-2 text-gray-700 text-sm mb-3">
              <p><span class="font-semibold text-gray-900">Qualification:</span> <?= htmlspecialchars($faculty['degree']) ?></p>
              <p class="specialization-scroll"><span class="font-semibold text-gray-900">Specialization:</span> <?= htmlspecialchars($faculty['specialization']) ?></p>
              <p><span class="font-semibold text-gray-900">Experience:</span> <?= htmlspecialchars($faculty['experience']) ?></p>
            </div>

            <!-- Divider -->
            <div class="border-t my-2"></div>

            <!-- Contact -->
            <div class="text-sm text-center">
              <p>
                <span class="font-semibold">Email: </span>
                <a href="mailto:<?= htmlspecialchars($faculty['email']) ?>" class="text-blue-600 hover:underline">
                  <?= htmlspecialchars($faculty['email']) ?>
                </a>
              </p>
            </div>
          </div>
      <?php
        }
      } else {
        echo '<p class="text-center text-red-500">No data found or API error.</p>';
      }
      ?>
    </div>
  </div>
</div>

<!-- Tabs JavaScript -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const buttons = document.querySelectorAll(".tab-btn");
    const cards = document.querySelectorAll(".faculty-card");
    let prevActiveBtn = null;

    function filterData(dept) {
      cards.forEach(card => {
        if (dept === "ALL" || card.getAttribute("data-dept") === dept) {
          card.style.display = "flex";
        } else {
          card.style.display = "none";
        }
      });

      // Remove active from all buttons
      buttons.forEach(b => b.classList.remove("active"));
    }

    buttons.forEach(btn => {
      btn.addEventListener("click", () => {
        const dept = btn.getAttribute("data-dept");
        filterData(dept);

        // Set active state
        if (prevActiveBtn) prevActiveBtn.classList.remove("active");
        btn.classList.add("active");
        prevActiveBtn = btn;
      });
    });

    // Default show ALL
    const defaultBtn = document.querySelector('[data-dept="ALL"]');
    if (defaultBtn) defaultBtn.click();
  });
</script>

<?php include 'includes/footer.php'; ?>