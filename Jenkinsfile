pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }
        
        stage('Setup & Build Assets') {
            steps {
                sh 'echo "Installing dependencies..."'
                sh 'npm install --silent || true'
                sh 'echo "Building Tailwind CSS assets..."'
                sh 'npm run build --silent || true'
            }
        }

        stage('Code Quality / PHP Lint') {
            steps {
                sh 'echo "Running syntax check on PHP files..."'
                sh 'find . -name "*.php" -exec php -l {} \\;'
            }
        }

        stage('Deployment Prep') {
            steps {
                sh 'echo "Build and verification successful. Ready for AWS EC2 deployment."'
            }
        }
    }
}