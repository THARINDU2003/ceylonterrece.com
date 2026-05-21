<?php
session_start();
require_once 'db.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accountType = $_POST['accountType'] ?? 'personal';
    $companyName = $_POST['companyName'] ?? '';
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $terms = isset($_POST['terms']) ? true : false;

    if (empty($name) || empty($email) || empty($password) || !$terms) {
        $message = "Please fill in all required fields and accept terms.";
        $messageType = "error";
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $message = "Email address is already registered.";
            $messageType = "error";
        } else {
            // Insert new user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, account_type, company_name) VALUES (?, ?, ?, ?, ?)");
            try {
                $stmt->execute([$name, $email, $hashedPassword, $accountType, $companyName]);
                $userId = $pdo->lastInsertId();
                
                // Auto login
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_role'] = 'user';
                
                header("Location: user-home.php");
                exit;
            } catch (PDOException $e) {
                $message = "Registration failed: " . $e->getMessage();
                $messageType = "error";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - CeylonTerrece.com</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
    <script>if(localStorage.getItem('theme')==='dark'){document.documentElement.classList.add('dark-theme');}</script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full p-8 bg-white shadow-2xl rounded-2xl">
        <div class="text-center mb-8">
            <a href="/" class="inline-block">
                <video src="/images/Logo_name_animated_202604111211.mp4" autoplay loop muted playsinline class="h-20 mx-auto mb-4 rounded-lg pointer-events-none"></video>
            </a>
            <h2 class="text-3xl font-bold text-gray-800">Create Account</h2>
            <p class="text-gray-500">Join the best property marketplace in Sri Lanka</p>
        </div>

        <?php if ($message): ?>
            <div class="mb-4 p-4 rounded-lg <?php echo $messageType === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="signup.php" class="space-y-4 ct-secure-form">
            
            <!-- Account Type Selection -->
            <div class="flex gap-4 mb-2">
                <label class="flex-1 cursor-pointer text-gray-600 hover:text-blue-600">
                    <input type="radio" name="accountType" value="personal" class="peer hidden" checked onchange="toggleCompanyField()">
                    <div class="text-center py-2 border-2 border-gray-200 rounded-xl peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-600 font-bold transition-all">
                        <i class="fas fa-user-circle text-xl mb-1 block"></i> Personal
                    </div>
                </label>
                <label class="flex-1 cursor-pointer text-gray-600 hover:text-blue-600">
                    <input type="radio" name="accountType" value="company" class="peer hidden" onchange="toggleCompanyField()">
                    <div class="text-center py-2 border-2 border-gray-200 rounded-xl peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-600 font-bold transition-all">
                        <i class="fas fa-building text-xl mb-1 block"></i> Company
                    </div>
                </label>
            </div>

            <!-- Company Name Field (Hidden by default) -->
            <div id="companyField" class="hidden">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Company or Agency Name</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-briefcase"></i>
                    </span>
                    <input type="text" name="companyName" id="companyName" 
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none" 
                           placeholder="CeylonTerrece LLC">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Your Full Name</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" name="name" required 
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none" 
                           placeholder="John Doe">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" name="email" required 
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none" 
                           placeholder="your@email.com">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Create Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" required 
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none" 
                           placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-start gap-2 pt-2">
                <input type="checkbox" name="terms" id="terms" required class="mt-1 w-4 h-4 text-blue-600 rounded">
                <label for="terms" class="text-xs text-gray-600">
                    I agree to the <a href="#" class="text-blue-600 hover:underline">Terms of Service</a> and <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>
                </label>
            </div>

            <button type="submit" 
                    class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl hover:bg-blue-700 transform transition-all active:scale-95 shadow-lg mt-4">
                Sign Up Now
            </button>
        </form>

        <p class="mt-8 text-center text-gray-600">
            Already have an account? 
            <a href="login.php" class="font-bold text-blue-600 hover:text-blue-700">Log in</a>
        </p>
    </div>

    <script src="/js/theme.js"></script>
    <script>
        function toggleCompanyField() {
            const isCompany = document.querySelector('input[name="accountType"]:checked').value === 'company';
            const companyField = document.getElementById('companyField');
            const companyInput = document.getElementById('companyName');
            
            if (isCompany) {
                companyField.classList.remove('hidden');
                companyInput.required = true;
            } else {
                companyField.classList.add('hidden');
                companyInput.required = false;
            }
        }
    </script>
</body>
</html>
