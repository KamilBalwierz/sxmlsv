# SimleXML Simple Validator

Extremely simple plugin for powerful n98-magerun tool that will find mallformed XML files in Your Magento project.

## Instalation

All possible ways to do this are in the [MageRun docs](http://magerun.net/introducting-the-new-n98-magerun-module-system/)
Following is my favourite method:


1. Create `~/.n98-magerun/modules/` if it doesn't already exist. (or `/usr/local/share/n98-magerun/modules` if you prefer that)
```
mkdir -p ~/.n98-magerun/modules/
```
2. Clone plugin repository in there. 
```
git clone git@github.com:KamilBalwierz/sxmlsv.git ~/.n98-magerun/modules/sxmlsv
```
3. It should be installed.To see that it was installed, check to see if one of the new commands is in there.
```
n98-magerun sxmlsv:scan
```

## Usage

```
n98-magerun sxmlsv:scan [directoryToScan]
```

There is only one command that will scan recursevly given directory or current directory if non specified.
It will attempt to parse all `*.xml` files that it finds and will output fiels that were not loaded correclty.
And thats all, no more sophisticated logic inside.
