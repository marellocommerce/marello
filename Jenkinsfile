#!/usr/bin/env groovy

@Library('global-pipeline@develop') _

pipeline {
    agent any
    options {
        // Configure job retention policy -
        buildDiscarder(logRotator(artifactNumToKeepStr: '25', numToKeepStr: '25', daysToKeepStr: '7', artifactDaysToKeepStr: '7'))
        ansiColor colorMapName: 'xterm'
        disableConcurrentBuilds()
    }
    environment {
        TEST_AUTH               = ''
        PROD_AUTH               = ''
        STAGE_AUTH              = ''
        GITHUB_OAUTH_TOKEN      = credentials('marello-builder')
        STAGE_HOST              = ''
        PROD_HOST               = ''
        TEST_HOST               = ''
        ANSIBLE_FORCE_COLOR     = 'true'
        PHP_BIN                 = '$(which php)'
        DEP_BIN                 = ''
        DEPLOY_STAGE_PATH       = ''
        DEPLOY_TEST_PATH        = ''
        DEPLOY_PROD_PATH        = ''
        TMP_DIR_PATH            = '~/'
        SLACK_CHANNEL           = ''
        DOCKER_APP_ROOT         = '/app'
        DOCKER_COMPOSE          = 'docker-compose -f environment/docker-compose-marello-ce.yml'
        COMPOSE_PROJECT_NAME    = "MARELLO_MONO_REPO"
    }
    stages {
        stage('Building') {
            steps {
                sendNotifications 'STARTED'
                sh 'docker network prune -f'
                sh "$DOCKER_COMPOSE up -d --build"
                sh '$DOCKER_COMPOSE exec -T web bash -c "COMPOSER=dev.json composer install --prefer-dist;"'
            }
        }

        stage('Testing') {
            steps {
                parallel (
                    phpunit: {
                        sh '$DOCKER_COMPOSE exec -T web bash -c "php ./bin/phpunit -c app --tap --color --testsuite="Unit Tests"'
                    },
                    phplint: {
                        sh '$DOCKER_COMPOSE exec -T web bash -c "php ./bin/phpcs vendor/marellocommerce/marello -p --encoding=utf-8 --extensions=php --standard=psr2 --report=checkstyle --report-file=app/logs/phpcs.xml'
                    }
                )
            }
        }
    }
    post {
        always {
            sendNotifications currentBuild.result
            sh '$DOCKER_COMPOSE exec -T web bash -c "rm -rf $DOCKER_APP_ROOT/*;"'
            sh 'git checkout environment/docker-compose-marello-ce.yml'
            sh "$DOCKER_COMPOSE down || true"
            deleteDir()
        }
    }
}