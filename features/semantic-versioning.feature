Feature: Calculating a version number based on semantic versioning

  Scenario: Releasing a major version
    Given the latest release on this branch is "1.2.1"
    When I release a new major version
    Then version "2.0.0" should be released

  Scenario: Releasing a minor version
    Given the latest release on this branch is "1.2.1"
    When I release a new minor version
    Then version "1.3.0" should be released

  Scenario: Releasing a patch version
    Given the latest release on this branch is "1.4.9"
    When I release a new patch version
    Then version "1.4.10" should be released

  Scenario: Releasing an alpha version of a major release
    Given the latest release on this branch is "1.0.0"
    When I release an alpha version of a new major release
    Then version "2.0.0-alpha.1" should be released

  Scenario: Releasing a consecutive alpha version
    Given the latest release on this branch is "2.0.0-alpha.1"
    When I release an alpha version
    Then version "2.0.0-alpha.2" should be released

  Scenario: Releasing a beta version
    Given the latest release on this branch is "2.0.0-alpha.5"
    When I release a beta version
    Then version "2.0.0-beta.1" should be released

  Scenario: Releasing a release candidate
    Given the latest release on this branch is "3.0.0-beta.3"
    When I release a release candidate
    Then version "3.0.0-rc.1" should be released

  Scenario: Releasing after a pre-release
    Given the latest release on this branch is "2.0.0-beta.2"
    When I release a new version
    Then version "2.0.0" should be released

  Scenario: Releasing after a minor pre-release
    Given the latest release on this branch is "1.2.0-rc.1"
    When I release a new version
    Then version "1.2.0" should be released
