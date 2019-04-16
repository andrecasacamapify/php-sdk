#!groovy​

pipeline {
    agent { 
        label 'jenkins-slave' 
    }
    environment {
        VERSION = "0.0.${BUILD_NUMBER}"
        PACKAGIST_PACKAGE_URL = "https://packagist.org/packages/mapify/sdk"
        TEST_BASE_URI_QA = "https://authentication.qa-api.mapify.ai"
        TEST_BASE_URI_PRODUCTION = "https://authentication.api.mapify.ai"
    }
    stages {
        stage('Setup') {
            steps {
                sh "git clean -xfd"
            }
        }

        stage('Build') {
            steps {
                sh "pipeline/scripts/01-build.sh ${VERSION} 5.6"
                sh "pipeline/scripts/01-build.sh ${VERSION} 7"
            }
        }

        stage('Test QA') {
            steps {
                withCredentials([
                    string(credentialsId: 'mapify-authorization-valid-api-key', variable: 'APIKEY'),
                    string(credentialsId: 'mapify-authentication-jwt-public-key', variable: 'TEST_PUBLIC_KEY_BASE64')
                ]) {
                    sh "pipeline/scripts/02-test.sh ${TEST_PUBLIC_KEY_BASE64} ${TEST_BASE_URI_QA} ${VERSION} ${APIKEY} 5.6"
                    sh "pipeline/scripts/02-test.sh ${TEST_PUBLIC_KEY_BASE64} ${TEST_BASE_URI_QA} ${VERSION} ${APIKEY} 7"
                }
            }
        }

        stage('Test Production') {
            steps {
                withCredentials([
                    string(credentialsId: 'mapify-authorization-valid-api-key-production', variable: 'APIKEY'),
                    string(credentialsId: 'mapify-authentication-jwt-public-key-production', variable: 'TEST_PUBLIC_KEY_BASE64_PRODUCTION')
                ]) {
                    sh "pipeline/scripts/02-test.sh ${TEST_PUBLIC_KEY_BASE64_PRODUCTION} ${TEST_BASE_URI_PRODUCTION} ${VERSION} ${APIKEY} 5.6"
                    sh "pipeline/scripts/02-test.sh ${TEST_PUBLIC_KEY_BASE64_PRODUCTION} ${TEST_BASE_URI_PRODUCTION} ${VERSION} ${APIKEY} 7"
                }
            }
        }

        stage('Publish') {
            steps {
                sshagent(credentials : ['mapify-github']) {
                    withCredentials([
                        usernamePassword(credentialsId: 'sdk-php-packagist-user-apikey', usernameVariable: 'PACKAGIST_USERNAME', passwordVariable: 'PACKAGIST_API_TOKEN')
                    ]) {
                        sh "pipeline/scripts/03-publish.sh ${PACKAGIST_USERNAME} ${PACKAGIST_API_TOKEN} ${PACKAGIST_PACKAGE_URL} ${VERSION}"
                    }
                }
            }
        }
    }
}