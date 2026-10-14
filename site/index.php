<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register & Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full space-y-8 p-10 bg-white rounded-xl shadow-lg">
        <!-- Sign Up Form -->
        <div id="signup" class="hidden">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Register
            </h2>
            <form class="mt-8 space-y-6" action="register.php" method="POST">
                <div class="space-y-6">
                    <div>
                        <label for="fName" class="block text-sm font-medium text-gray-700">First Name</label>
                        <input id="fName" name="fName" type="text" required class="appearance-none rounded-md block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="First Name">
                    </div>
                    <div>
                        <label for="lName" class="block text-sm font-medium text-gray-700">Last Name</label>
                        <input id="lName" name="lName" type="text" required class="appearance-none rounded-md block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Last Name">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input id="email" name="email" type="email" autocomplete="email" required class="appearance-none rounded-md block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Email Address">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none rounded-md block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Password">
                    </div>
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                        <select id="role" name="role" required class="appearance-none rounded-md block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="student">Student</option>
                            <option value="admin">Admin</option>
                            <option value="worker">Worker</option>
                            <option value="lecturer">Lecturer</option>
                        </select>
                    </div>
                </div>

                <div>
                    <button type="submit" name="signUp" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Sign Up
                    </button>
                </div>
            </form>
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">Already have an account?</p>
                <button id="signInButton" class="font-medium text-indigo-600 hover:text-indigo-500">Sign In</button>
            </div>
        </div>

        <!-- Sign In Form -->
        <div id="signIn">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Sign In
            </h2>
            <form class="mt-8 space-y-6" action="register.php" method="POST">
                <div class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input id="email" name="email" type="email" autocomplete="email" required class="appearance-none rounded-md block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Email Address">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none rounded-md block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Password">
                    </div>
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                        <select id="role" name="role" required class="appearance-none rounded-md block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="student">Student</option>
                            <option value="admin">Admin</option>
                            <option value="worker">Worker</option>
                            <option value="lecturer">Lecturer</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="text-sm">
                        <a href="recover.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                            Forgot your password?
                        </a>
                    </div>
                </div>

                <div>
                    <button type="submit" name="signIn" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Sign In
                    </button>
                </div>
            </form>
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">Don't have an account yet?</p>
                <button id="signUpButton" class="font-medium text-indigo-600 hover:text-indigo-500">Sign Up</button>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
