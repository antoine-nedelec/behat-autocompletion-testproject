Feature:
    In order to test the login page
    As a random user
    I test the login page

    @login
    Scenario: I am redirected on the login page when random user access homepage
        When I go to "/"
        Then I should be on "/login"
        And