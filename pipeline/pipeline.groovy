#!groovy​

pipeline {
    agent { 
        label 'jenkins-slave' 
    }
    environment {
        VERSION = "0.0.x"
        PACKAGIST_PACKAGE_URL = "https://packagist.org/packages/mapify/sdk"
        TEST_BASE_URI_QA = "authentication.api-qa.mapify.ai"
        TEST_BASE_URI_PRODUCTION = "authentication.api.mapify.ai"
    }
    stages {

        stage('Setup') {
            steps {
                sh "git clean -xfd"
            }
        }

        stage('Build') {
            steps {
                sh "pipeline/scripts/01-build.sh ${VERSION}"
            }
        }

        stage('Test QA') {
            withCredentials([
                string(credentialsId: 'mapify-authentication-jwt-public-key', variable: 'TEST_PUBLIC_KEY_BASE64'),
            ]) {
                steps {
                    sh "pipeline/scripts/02-test.sh ${TEST_VALID_API_KEY} ${TEST_PUBLIC_KEY_BASE64} ${TEST_BASE_URI_QA} ${VERSION}"
                }
            }
        }

        stage('Test Production') {
            withCredentials([
                string(credentialsId: 'mapify-authentication-jwt-public-key-production', variable: 'TEST_PUBLIC_KEY_BASE64'),
            ]) {
                steps {
                    sh "pipeline/scripts/02-test.sh ${TEST_VALID_API_KEY} ${TEST_PUBLIC_KEY_BASE64} ${TEST_BASE_URI_PRODUCTION} ${VERSION}"
                }
            }
        }

        stage('Publish') {
            withCredentials([
                usernamePassword(credentialsId: 'sdk-php-packagist-user-apikey', usernameVariable: 'PACKAGIST_USERNAME', passwordVariable: 'PACKAGIST_API_TOKEN')
            ]) {
                steps {
                    sh "pipeline/scripts/02-publish.sh ${PACKAGIST_USERNAME} ${PACKAGIST_API_TOKEN} ${PACKAGIST_PACKAGE_URL} ${VERSION}"
                }
            }
        }
    }
}