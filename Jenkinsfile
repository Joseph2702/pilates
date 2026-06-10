// ============================================================================
// Jenkinsfile — Pipeline CI/CD untuk Femm Pilates
//
// Alur:
//   1. Checkout    → ambil source code terbaru dari GitHub
//   2. Build Test  → build Docker image khusus testing (punya dev deps)
//   3. Test        → jalankan PHPUnit di dalam container (isolated)
//   4. Lint        → cek code style dengan Laravel Pint
//   5. Build Prod  → build Docker image production
//   6. Deploy      → docker compose up -d (restart container aplikasi)
//
// Jika stage Test/Lint GAGAL, pipeline berhenti — Deploy TIDAK akan jalan.
// Ini inti dari CI/CD: kode buruk tidak pernah sampai ke "production".
// ============================================================================

pipeline {
    agent any

    // Variabel yang dipakai berulang kali di seluruh pipeline
    environment {
        TEST_IMAGE = "pilates-test:${env.BUILD_NUMBER}"
        APP_IMAGE  = "pilates-app:${env.BUILD_NUMBER}"
    }

    options {
        // Hapus workspace lama sebelum checkout, biar selalu bersih
        skipDefaultCheckout(false)
        // Simpan log maksimal 10 build terakhir saja (hemat disk)
        buildDiscarder(logRotator(numToKeepStr: '10'))
    }

    stages {

        // ── STAGE 1: Checkout ────────────────────────────────────────────
        stage('Checkout') {
            steps {
                echo "Mengambil source code terbaru dari GitHub..."
                checkout scm
            }
        }

        // ── STAGE 2: Build Test Image ────────────────────────────────────
        stage('Build Test Image') {
            steps {
                echo "Building Docker image untuk testing (target: test)..."
                sh "docker build --target test -t ${TEST_IMAGE} ."
            }
        }

        // ── STAGE 3: Run PHPUnit Tests ───────────────────────────────────
        stage('Run Tests') {
            steps {
                echo "Menjalankan PHPUnit tests di dalam container..."
                sh "docker run --rm ${TEST_IMAGE} php artisan test"
            }
        }

        // ── STAGE 4: Lint (Laravel Pint) ─────────────────────────────────
        stage('Lint') {
            steps {
                echo "Mengecek code style dengan Laravel Pint..."
                sh "docker run --rm ${TEST_IMAGE} ./vendor/bin/pint --test"
            }
        }

        // ── STAGE 5: Build Production Image ──────────────────────────────
        // Hanya jalan jika branch main DAN semua test/lint di atas lulus
        stage('Build Production Image') {
            when {
                branch 'main'
            }
            steps {
                echo "Building Docker image production..."
                sh "docker build --target production -t ${APP_IMAGE} -t pilates-app:latest ."
            }
        }

        // ── STAGE 6: Deploy via Docker Compose ───────────────────────────
        stage('Deploy') {
            when {
                branch 'main'
            }
            steps {
                echo "Deploying aplikasi via docker compose..."
                sh '''
                    docker compose down
                    docker compose up -d --build
                '''
            }
        }
    }

    // ── Post-pipeline actions ────────────────────────────────────────────
    post {
        always {
            echo "Membersihkan image test sementara..."
            sh "docker rmi ${TEST_IMAGE} || true"
        }
        success {
            echo "✅ Pipeline SUKSES! App berjalan di http://localhost:8000"
        }
        failure {
            echo "❌ Pipeline GAGAL — cek log di atas untuk detail error."
        }
    }
}
