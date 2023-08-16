# Generate Test Spec for Craft CMS

A Craft module that generates a test specification from PEST PHP test results.

![Test specification](https://putyourlightson.com/assets/images/articles/blitz-test-spec-screenshot.png)

See an [example](https://github.com/putyourlightson/craft-blitz/blob/develop/tests/TESTS.md).

## Installation

Install this package via composer.

```shell
composer require putyourlightson/craft-generate-test-spec --dev
```

## Usage

This module looks for a `test-results.xml` file (JUnit XML format) in the test path and generates a test spec at `../TESTS.md`.

```shell
php craft generate-test-spec/markdown path/to/tests
```

The following command can be used to have Pest produce the `test-results.xml` file in the required JUnit XML format.

```shell
php craft pest/test --test-directory=path/to/tests --log-junit=path/to/tests/test-results.xml
```

## License

This module is licensed for free under the MIT License.

---

Created by [PutYourLightsOn](https://putyourlightson.com/).
