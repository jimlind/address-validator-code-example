# Address Validator Code Example

##Prerequisite
A system with PHP installed. Code was written and tested using PHP 8.1 but should run on PHP 7.4 as
well since the used vendors support that as well. If you are running Mac OSX brew will get you a suitable
PHP runtime very easily.

##Running
###Step 1. Setup your local project.
Clone the Git repository to your local machine.
In the config directory make a copy of parameters.yaml.dist and rename it parameters.yaml. In that file replace `Your Address Validator API Key` with your actual API Key.
You may need to `chmod +x app` and `chmod +x test` depending on your system.

###Step 2. Install dependencies.
`>php composer.phar install`

###Step 3. Run the unit tests.
`>./test`

###Step 4. Validate addresses.
`>./app validation data/input.csv`

##Further Explanation
The problem description mentioned stubs and mocks and so immediatly I knew I needed to get into some dependency injection. If I was going to do dependency injection to write testable code I knew I needed to utilize a project that allowed me to autowire my dependencies. If I was going to write a command line tool, made sense for me to rely on all the classic tooling I knew from PHP. So Symfony components for all the heavy lifting and organization, Guzzle for taking care of network requests, and PHPUnit to take care of some tests.
To maximize spending time testing the most important logic and not plumbing I only wrote unit tests for the ApiHelper and the OutputHelper.
Most of the rest of the decisions made were just following the existing Symfony project best practices.
