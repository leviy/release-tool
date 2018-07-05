Feature: Calculating a version number based on semantic versioning

  Scenario: Releasing a major version
    Given a release on this branch with version "1.0.0"
    When I release a new major version
    Then version "2.0.0" should be released

  Scenario: Releasing a minor version
    Given a release on this branch with version "1.0.0"
    When I release a new minor version
    Then version "1.1.0" should be released

  Scenario: Releasing a patch version
    Given a release on this branch with version "1.0.9"
    When I release a new patch version
    Then version "1.0.10" should be released

  Scenario: Releasing an alpha version of a major release
    Given a release on this branch with version "1.0.0"
    When I release an alpha version of a new major release
    Then version "2.0.0-alpha.1" should be released

  Scenario: Releasing a consecutive alpha version
    Given a release on this branch with version "2.0.0-alpha.1"
    When I release an alpha version
    Then version "2.0.0-alpha.2" should be released

  Scenario: Releasing a beta version
    Given a release on this branch with version "2.0.0-alpha.5"
    When I release a beta version
    Then version "2.0.0-beta.1" should be released

  Scenario: Releasing a release candidate
    Given a release on this branch with version "3.0.0-beta.3"
    When I release a release candidate
    Then version "3.0.0-rc.1" should be released

  Scenario: Releasing after a pre-release
    Given a release on this branch with version "2.0.0-beta.2"
    When I release a new major version
    Then version "2.0.0" should be released
