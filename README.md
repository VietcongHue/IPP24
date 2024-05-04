# IPP - PHP Project Core

**Authors:** 
Radim Kocman, 
Zbyněk Křivka

This README describes the basic structure of the project, briefly introduces recommended tools and shows how to set up a suitable development environment.
Further information can be found in the project specification and in the source code itself.

## Basic Structure

The Core of the project guides the execution of the script, handles the processing of exceptions and solves basic I/O operations. The Student part is thus focused primarily on the interpretation of IPPcode24.

The structure of the project is as follows:
- `core/` - This directory contains all Core classes from the namespace `\IPP\Core`. Files in this directory should not be modified.
- `student/` - This directory should contain all Student classes, and only its content will be submitted as an archive for evaluation. All Student classes should be in the namespace `\IPP\Student`.
- `interpret.php` - This file initializes the script environment and serves as an entry point to the object-oriented part of the program. This file should not be modified.
- other directories and files are related to the tools described later

You should not modify any files from the Core namespace. However, you are free to extend these classes in the Student namespace and modify their behavior. You should study the code of Core classes to see how they work, and how they can be used and extended. For example, see notes in the Engine class.

## About Tools

This is a brief overview of tools recommended for the project. It is not required to install and use every tool mentioned here. 

### Text Editor

Generally, it is possible to use any text editor of your choice.
However, we highly recommend to use editors that are able to analyze PHP code and assist you with code hints and code completion.
This is not just useful but almost essential for object-oriented programming that splits code into many separate files.

We can recommend Visual Studio Code (https://code.visualstudio.com/) as a modern text editor which also offers additional useful capabilities such as remote development via SSH or local development in a consistent environment with development containers.

Recommended VS Code extensions:
- PHP Intelephense - for better PHP analysis and hinting
- PHP Debug - for PHP debugging with Xdebug
- Code Spell Checker - for basic spell checking (Czech is also supported as an additional extension)
- Remote - SSH - for remote development via SSH
- Dev Containers - for local development in containers

### Composer

Composer (https://getcomposer.org/) is a very popular tool for dependency management in PHP. In this project, we use it very lightly just to define autoloading compliant with PSR-4 (https://www.php-fig.org/psr/psr-4/) and to handle two development dependencies. If we allow any additional libraries, they will be also managed with this tool.

To avoid complications with the installation in different environments, we have included this tool into the project as `composer.phar` that can be run as a regular script in PHP. Additionally, there is `composer.json` that defines autoloading and dependencies and `composer.lock` that holds precise versions of installed libraries.

With the command `install` this tool creates `vendor` directory with required files.

### PHPStan

PHPStan (https://phpstan.org/) is a well-established tool for static analysis of PHP code. It can find errors in your code without actually running it. When set to higher rule levels, it also forces programmers to properly annotate and type hint their code. This is beneficial not just for the static analysis but also for the understandability of your code in general. This is especially true for object-oriented programming, because without types editors are not able to properly assist with code hints and code completion.

In newer versions of PHP, it is possible to write many types natively in the code. However, there are still situations in which types cannot be specified precisely in the native way (such as arrays). In these cases, you can write the precise type as a PHPDoc comment (see https://phpstan.org/user-guide/troubleshooting-types).

The configuration of PHPStan is defined in `phpstan.neon`. You can run the static analysis with the command `vendor/bin/phpstan` (or `php8.3 vendor/bin/phpstan` for Merlin). PHPStan cache is stored in `tmp` directory.

### Docker

Docker (https://www.docker.com/) represents an ecosystem of tools that allow users to run isolated (mostly Linux-based) containers in the large variety of environments. For the purpose of this project, we are interested only with Docker Desktop (https://www.docker.com/products/docker-desktop/) as a prerequisite for Development Containers.

### Development Containers

Development Containers (https://containers.dev/) is an open specification that combines docker containers with text editors to create seamless and consistent full-featured development environments. 
For VS Code, you can use the following detailed guide: https://code.visualstudio.com/docs/devcontainers/containers.

The configuration of a development container suitable for this project is located in `.devcontainer` directory. There is also a launch configuration for the debug extension in `.vscode` directory.

## Development Environments

There are multiple viable options when it comes to a suitable development environment for this project. Choose one that fits you the most.

### Option 1: Development Container

It is possible to use the prepared development container to do the development locally in an isolated environment.
Inside the container, VS Code will automatically install all recommended extensions and resolve dependencies. Xdebug will be also ready for use.

Steps for VS Code:
- follow the installation steps in https://code.visualstudio.com/docs/devcontainers/containers until you have the Dev Containers extension prepared
- open the project directory in VS Code
- run the command "Dev Containers: Reopen in Container"
- you should be now able to use a terminal in VS Code to execute commands in the container
- run `php interpret.php --help` to try the project execution

### Option 2: Merlin

It is possible to do the whole development on the student server Merlin.

Until recently, it was also possible to easily use the remote development capabilities of VS Code on Merlin. However, VS Code version >= 1.86 now requires glibc >= 2.28, and Merlin does not meet this requirement.
It is still possible to use the older version of VS Code, but you should use it in a portable mode or disable updates. For more info see:
https://code.visualstudio.com/docs/remote/faq#_can-i-run-vs-code-server-on-older-linux-distributions

Steps for VS Code 1.85:
- follow https://code.visualstudio.com/docs/remote/ssh until you successfully connect VS Code and your project directory on Merlin
- you should be now able to use a terminal in VS Code to execute commands on Merlin
- run `php8.3 composer.phar install` to install dependencies
- run `php8.3 interpret.php --help` to try the project execution
- optionally install other recommended VS Code extensions

### Option 3: Custom

Of course, it is also possible to install PHP directly on your main system or do the development in another custom way. However, in these cases, make sure that the result also works on Merlin. The configuration of PHP can affect the behavior of the script.
