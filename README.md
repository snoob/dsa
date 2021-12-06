# DSA data exporter

## Requirements

TaskFile : 
  * Installation : https://taskfile.dev/#/installation
  * Auto complete (optional) : 
    * Bash : https://github.com/bfarayev/task/blob/feature/autocomplete/completion/task.bash
    * Zsh : https://github.com/sawadashota/go-task-completion

## Installation

### Clone repositories

```
git clone git@github.com:snoob/dsa.git
```

### Setup your TaskFile.yml

If you don't use Symfony CLI, you must configure the TaskFile with your Composer and PHP executables :

```
vars:
    COMPOSER_EXEC: 'symfony composer'
    PHP_EXEC: 'symfony php'
```

Or you can install Symfony CLI : wget https://get.symfony.com/cli/installer -O - | bash

### Run install command

```
task install
```

### Configure your .env.local :

```bash
echo "CLUB_ID=YOUR_CLUB_TOKEN" >> .env.local
```



You are done !

## Export data

```
task start
```
