# This file contains a user story for demonstration only.
# Learn how to get started with Behat and BDD on Behat's website:
# http://behat.org/en/latest/quick_start.html

Feature: Login test

    Scenario: Login
        Given I am on the homepage
        When I go to "/en/login"
        And I fill in "email" with "test@author1.com"
        And I fill in "password" with "test"
        And I press "Sign in"
        Then I should go to the page '/en/article/'

    Scenario: Register
        Given I am on the homepage
        When I go to "/en/register"
        And I fill in "registration_form[email]" with "test@author9.com"
        And I fill in "registration_form[fullName]" with "Test User"
        And I fill in "registration_form[password][first]" with "test"
        And I fill in "registration_form[password][second]" with "test"
        And I press "Register"
        Then I should go to the page '/en/article/'
