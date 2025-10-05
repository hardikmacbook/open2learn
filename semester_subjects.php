<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/header.php';
include 'includes/api_helper.php';

// === Get URL Parameters ===
$courseId = $_GET['course_id'] ?? '';
$yearNumber = isset($_GET['year']) ? intval($_GET['year']) : 0;
$semesterNumber = isset($_GET['semester']) ? intval($_GET['semester']) : 0;

// Redirect if missing params
if (empty($courseId) || $yearNumber === 0 || $semesterNumber === 0) {
    header('Location: courses.php');
    exit;
}

// Get course data from API
$course = findCourseById($courseId);
if (!$course) {
    include 'includes/error_state.php';
    exit;
}

// Find year
$year = null;
foreach ($course['years'] as $y) {
    if ($y['year_number'] === $yearNumber) {
        $year = $y;
        break;
    }
}
if (!$year) {
    include 'includes/error_state.php';
    exit;
}

// Find semester
$semester = null;
foreach ($year['semesters'] as $s) {
    if ($s['semester_number'] === $semesterNumber) {
        $semester = $s;
        break;
    }
}
if (!$semester) {
    include 'includes/error_state.php';
    exit;
}

// Count subjects for dynamic grid
$subjectCount = count($semester['subjects'] ?? []);
?>

<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-blue-50 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10 pointer-events-none select-none">
        <div class="absolute inset-0"
             style="background-image: radial-gradient(circle at 25% 25%, #1E3A8A 0%, transparent 50%),
                       radial-gradient(circle at 75% 75%, #6366F1 0%, transparent 50%);"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-6 py-10">
        <!-- Navigation -->
        <div class="mb-8">
            <a href="year_semesters.php?course_id=<?= urlencode($courseId) ?>&year=<?= urlencode($yearNumber) ?>"
               class="inline-flex items-center text-[#1E3A8A] hover:text-[#BFA14A] font-medium transition-all duration-200 group mt-10">
                <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Semesters
            </a>
        </div>

        <!-- Course, Year, Semester Header -->
        <div class="bg-white rounded-3xl border border-gray-200 overflow-hidden shadow-lg mb-12">
            <div class="p-10">
                <div class="flex flex-col lg:flex-row items-start gap-8">
                    <div class="flex-1">
                        <!-- Badges -->
                        <div class="flex flex-wrap gap-3 mb-6">
                            <span class="px-4 py-2 bg-gradient-to-r from-indigo-100 to-blue-100 rounded-full text-indigo-700 font-semibold text-sm">
                                Year <?= htmlspecialchars($yearNumber) ?>
                            </span>
                            <span class="px-4 py-2 bg-gradient-to-r from-purple-100 to-pink-100 rounded-full text-purple-700 font-semibold text-sm">
                                Semester <?= htmlspecialchars($semesterNumber) ?>
                            </span>
                        </div>

                        <!-- Title -->
                        <h1 class="text-4xl lg:text-5xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent mb-4">
                            <?= htmlspecialchars($course['title']) ?>
                        </h1>

                        <!-- Instructor -->
                        <div class="flex items-center text-lg text-gray-700 mt-4">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <?= htmlspecialchars($course['instructor']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subjects Section -->
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-6">Subjects</h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Browse all available subjects for Semester <?= htmlspecialchars($semesterNumber) ?> in Year <?= htmlspecialchars($yearNumber) ?>
            </p>
        </div>

        <?php if (!empty($semester['subjects'])): ?>
            <!-- Dynamic Grid Layout based on subject count -->
            <div class="<?php
                if ($subjectCount == 1) {
                    echo 'flex justify-center';
                } elseif ($subjectCount == 2) {
                    echo 'grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto items-start';
                } else {
                    echo 'grid gap-8 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 items-start';
                }
            ?>">
                <?php foreach ($semester['subjects'] as $subject): ?>
                    <div class="group relative flex flex-col bg-white rounded-3xl shadow-xl border border-gray-100 transform transition-all duration-300 hover:-translate-y-2 select-text <?php
                        if ($subjectCount == 1) echo 'max-w-md w-full h-[600px]';
                        elseif ($subjectCount == 2) echo 'w-full h-[600px]';
                        else echo 'h-[600px]';
                    ?>">
                        <!-- Subject Image -->
                        <div class="h-full w-full rounded-xl overflow-hidden mb-6 relative">
                            <?php if (!empty($subject['image'])): ?>
                                <img src="<?= htmlspecialchars($subject['image']) ?>" alt="<?= htmlspecialchars($subject['title']) ?>"
                                    class="w-[400px] h-full object-cover hover:scale-105 transition-transform duration-500"
                                    onerror="this.onerror=null;this.src='images/default-placeholder.png';">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 relative">
                                    <!-- Decorative pattern -->
                                    <div class="absolute inset-0 opacity-20">
                                        <div class="absolute top-4 left-4 w-8 h-8 border-2 border-blue-300 rounded-full"></div>
                                        <div class="absolute bottom-4 right-4 w-6 h-6 border-2 border-purple-300 rounded-full"></div>
                                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-12 h-12 border-2 border-indigo-300 rounded-lg rotate-45"></div>
                                    </div>
                                    <div class="relative z-10 text-center">
                                        <svg class="w-12 h-12 text-blue-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        <span class="text-blue-600 font-medium text-sm">Subject Material</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Subject Info -->
                        <div class="flex-grow px-6 pt-0">
                            <h3 class="text-2xl font-bold text-gray-900 mb-3 leading-tight"><?= htmlspecialchars($subject['title']) ?></h3>
                            <p class="text-gray-600 leading-relaxed line-clamp-3"><?= htmlspecialchars($subject['description']) ?></p>
                        </div>

                        <!-- Download Buttons Section -->
                        <div class="mt-auto px-6 pb-6 space-y-3 pt-2">
                            <!-- Google Drive PDF download (login check) -->
                            <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                                <a target="_blank" href="<?= htmlspecialchars($subject['pdf']) ?>" download
                                    class="inline-flex items-center justify-center w-full px-5 py-3 bg-[#1E3A8A] text-white font-semibold rounded-xl hover:bg-[#BFA14A] shadow-lg transition-all duration-200 group">
                                    <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Google Drive
                                </a>
                            <?php else: ?>
                                <a href="login.php"
                                    class="inline-flex items-center justify-center w-full px-5 py-3 bg-gray-300 text-gray-600 font-semibold rounded-xl shadow-lg hover:bg-[#BFA14A] cursor-pointer">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Login to unlock
                                </a>
                            <?php endif; ?>

                            <!-- Units PDF download (login check) -->
                            <?php if (!empty($subject['units'])): ?>
                                <div class="mt-4">
                                    <button onclick="toggleUnits('<?= htmlspecialchars($subject['id']) ?>')" 
                                        class="w-full px-4 py-2 bg-gray-100 hover:bg-[#BFA14A]/20 text-gray-700 font-medium rounded-lg transition-colors duration-200 flex items-center justify-between">
                                        <span>View Units (<?= count($subject['units']) ?>)</span>
                                        <svg id="arrow-<?= htmlspecialchars($subject['id']) ?>" class="w-4 h-4 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                    <div id="units-<?= htmlspecialchars($subject['id']) ?>" class="overflow-hidden transition-all duration-300 max-h-0">
                                        <div class="space-y-2 bg-gray-50 p-4 rounded-xl mt-3 max-h-40 overflow-y-auto custom-scrollbar">
                                            <?php foreach ($subject['units'] as $unit): ?>
                                                <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
                                                    <a target="_blank" href="<?= htmlspecialchars($unit['pdf']) ?>" download
                                                        class="flex items-center justify-between w-full px-4 py-3 bg-white border border-gray-200 rounded-lg hover:bg-[#BFA14A]/10 hover:border-[#BFA14A]/30 transition-all duration-200 group">
                                                        <span class="font-medium text-gray-800 text-sm">Unit <?= htmlspecialchars($unit['unit_number']) ?>: <?= htmlspecialchars($unit['title']) ?></span>
                                                        <svg class="w-4 h-4 text-[#1E3A8A] group-hover:text-[#BFA14A] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                        </svg>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="login.php"
                                                        class="flex items-center justify-between w-full px-4 py-3 bg-gray-200 border border-gray-300 rounded-lg text-gray-600 cursor-pointer hover:bg-[#BFA14A]/30 transition-all duration-200 group">
                                                        <span class="font-medium text-gray-600 text-sm">Unit <?= htmlspecialchars($unit['unit_number']) ?>: <?= htmlspecialchars($unit['title']) ?></span>
                                                        <svg class="w-4 h-4 text-gray-500 group-hover:text-[#BFA14A] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                        </svg>
                                                        <span class="ml-2">Login to unlock</span>
                                                    </a>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- JavaScript for collapsible units -->
            <script>
                function toggleUnits(subjectId) {
                    const unitsDiv = document.getElementById('units-' + subjectId);
                    const arrow = document.getElementById('arrow-' + subjectId);

                    if (unitsDiv.style.maxHeight === '0px' || unitsDiv.style.maxHeight === '') {
                        unitsDiv.style.maxHeight = '160px';
                        arrow.style.transform = 'rotate(180deg)';
                    } else {
                        unitsDiv.style.maxHeight = '0px';
                        arrow.style.transform = 'rotate(0deg)';
                    }
                }
            </script>
            <!-- Custom scrollbar styles -->
            <style>
                .custom-scrollbar::-webkit-scrollbar { width: 6px; }
                .custom-scrollbar::-webkit-scrollbar-track { background: #1E3A8A; border-radius: 3px; }
                .custom-scrollbar::-webkit-scrollbar-thumb { background: #1E3A8A; border-radius: 3px; }
                .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #1E3A8A; }
                .line-clamp-3 { display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden; }
            </style>
        <?php else: ?>
            <!-- Empty State -->
            <div class="text-center py-20">
                <div class="w-32 h-32 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-8 relative">
                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-blue-200 rounded-full"></div>
                    <div class="absolute -bottom-2 -left-2 w-4 h-4 bg-purple-200 rounded-full"></div>
                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 mb-4">No Subjects Available</h3>
                <p class="text-xl text-gray-600 max-w-md mx-auto mb-8">This semester does not have subjects configured yet.</p>
                <a href="year_semesters.php?course_id=<?= urlencode($courseId) ?>&year=<?= urlencode($yearNumber) ?>"
                   class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-gray-700 to-gray-900 text-white font-semibold rounded-xl hover:from-gray-800 hover:to-black shadow-lg transform hover:scale-105 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Semesters
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>