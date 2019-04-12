#!groovy​

pipeline {
    agent { 
        label 'jenkins-slave' 
    }
    environment {
        VERSION = "0.0.x"
        PACKAGIST_PACKAGE_URL = "https://packagist.org/packages/mapify/sdk"
        TEST_BASE_URI_QA = "https://authentication.qa-api.mapify.ai"
        TEST_BASE_URI_PRODUCTION = "https://authentication.api.mapify.ai"
    }
    stages {
        stage('Setup') {
            steps {
                sh "pipeline/scripts/01-setup.sh"
            }
        }

        stage('Build') {
            steps {
                sh "pipeline/scripts/02-build.sh ${VERSION}"
            }
        }

        stage('Test QA') {
            steps {
                withCredentials([
                    file(credentialsId: 'mapify-service-account-key', variable: 'FILE'),
                    string(credentialsId: 'mapify-authentication-jwt-public-key', variable: 'TEST_PUBLIC_KEY_BASE64')
                ]) {
                    sh "pipeline/scripts/03-test.sh ${TEST_PUBLIC_KEY_BASE64} ${TEST_BASE_URI_QA} ${VERSION} ${FILE}"
                }
            }
        }

        stage('Test Production') {
            steps {
                withCredentials([
                    file(credentialsId: 'mapify-service-account-key', variable: 'FILE'),
                    string(credentialsId: 'mapify-authentication-jwt-public-key-production', variable: 'TEST_PUBLIC_KEY_BASE64_PRODUCTION')
                ]) {
                    sh "pipeline/scripts/03-test.sh ${TEST_PUBLIC_KEY_BASE64_PRODUCTION} ${TEST_BASE_URI_PRODUCTION} ${VERSION} ${FILE}"
                }
            }
        }

        stage('Publish') {
            steps {
                sshagent(credentials : ['mapify-github']) {
                    withCredentials([
                        sshUserPrivateKey(credentialsId: 'ssh-credentials-id', keyFileVariable: 'keyfile'),
                        usernamePassword(credentialsId: 'sdk-php-packagist-user-apikey', usernameVariable: 'PACKAGIST_USERNAME', passwordVariable: 'PACKAGIST_API_TOKEN'),
                        usernamePassword(credentialsId: 'sdk-php-packagist-user-apikey', usernameVariable: 'PACKAGIST_USERNAME', passwordVariable: 'PACKAGIST_API_TOKEN')
                    ]) {
                        sh "pipeline/scripts/04-publish.sh ${PACKAGIST_USERNAME} ${PACKAGIST_API_TOKEN} ${PACKAGIST_PACKAGE_URL} ${VERSION}"
                    }
                }
            }
        }
    }
}