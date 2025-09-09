<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="index.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <title>Open2Learn</title>
</head>
<body class="min-h-screen bg-gray-100">
  <!-- Floating Glass Header -->

<header class="fixed top-4 left-1/2 transform -translate-x-1/2 lg:w-[40%] max-w-6xl z-50">
  <div class="backdrop-blur-xl bg-white/40 border border-white/20 shadow-lg rounded-2xl px-6 py-3 flex justify-between items-center gap-3">
    
  <!-- Logo -->
    <a href="index.php" class="text-xl font-bold text-gray-900 flex items-center">
       <i class="fas fa-book-open text-[#1E3A8A] pr-2"></i>
      <p class="text-[#1E3A8A]">Open<span class="text-[#BFA14A]">2</span>Learn</p>
    </a>

  <!-- Desktop Menu -->
    <nav class="hidden md:flex space-x-6 text-gray-800">
      <a href="index.php" class="hover:text-[#1E3A8A] transition">Home</a>
      <a href="about.php" class="hover:text-[#1E3A8A] transition">About</a>
      <a href="courses.php" class="hover:text-[#1E3A8A] transition">Courses</a>
      <a href="faculty.php" class="hover:text-[#1E3A8A] transition">Faculty</a>
      <a href="contact.php" class="hover:text-[#1E3A8A] transition">Contact</a>
    </nav>

  <!-- Mobile Button -->
    <button id="mobile-menu-btn" class="md:hidden text-2xl text-gray-700 hover:text-[#1E3A8A] transition">
      <i class="fas fa-bars"></i>
    </button>
  </div>
</header>

  <!-- SIDE MOBILE MENU (Glass Effect) -->
<div id="mobile-menu" class="fixed top-0 right-0 h-full w-80 backdrop-blur-lg bg-white/50 border-l border-white/20 shadow-lg transform translate-x-full transition-transform duration-300 z-50">
  <div class="p-5 border-b border-gray-200 flex justify-between items-center">
      
    <button id="close-menu" class="text-gray-700 text-2xl hover:text-red-500 transition">
      <i class="fas fa-times"></i>
    </button>
  </div>

  <nav class="flex flex-col p-5 space-y-5 text-lg font-medium text-gray-800">
    <a href="index.php" class="hover:text-[#1E3A8A] transition">🏠 Home</a>
    <a href="about.php" class="hover:text-[#1E3A8A] transition">ℹ️ About</a>
    <a href="courses.php" class="hover:text-[#1E3A8A] transition">📚 Courses</a>
    <a href="faculty.php" class="hover:text-[#1E3A8A] transition">👨‍🏫 Faculty</a>
    <a href="contact.php" class="hover:text-[#1E3A8A] transition">📞 Contact</a>
  </nav>
</div>

  <!-- DARK OVERLAY -->
<div id="overlay" class="hidden fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm z-40 transition-opacity"></div>

  <!-- JavaScript for Mobile Menu -->

<script>
  const menuBtn = document.getElementById("mobile-menu-btn");
  const mobileMenu = document.getElementById("mobile-menu");
  const overlay = document.getElementById("overlay");
  const closeBtn = document.getElementById("close-menu");

  menuBtn.addEventListener("click", () => {
    mobileMenu.classList.remove("translate-x-full");
    overlay.classList.remove("hidden");
  });

  closeBtn.addEventListener("click", () => {
    mobileMenu.classList.add("translate-x-full");
    overlay.classList.add("hidden");
  });

  overlay.addEventListener("click", () => {
    mobileMenu.classList.add("translate-x-full");
    overlay.classList.add("hidden");
  });
</script>

</body>
</html>
