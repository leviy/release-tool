Feature: Changelog

  Scenario: Releasing a version without any pre-releases
    Given pull request "Some PR" with number 1 was merged
    When I release version "1.1.0"
    Then a release with title "1.1.0" should be published on GitHub with the following release notes:
      """
      # Changelog for 1.1.0

      * Some PR (pull request #1)

      """

  Scenario: Releasing a version which is preceded by a pre-release
    Given pull request "First pull request" with number 1 was merged
    And the pre-release "1.0.0-beta.1" was created
    And pull request "Fix bug that came out of beta testing" with number 2 was merged
    When I release version "1.0.0"
    Then a release with title "1.0.0" should be published on GitHub with the following release notes:
      """
      # Changelog for 1.0.0

      * Fix bug that came out of beta testing (pull request #2)

      # Changelog for 1.0.0-beta.1

      * First pull request (pull request #1)

      """
