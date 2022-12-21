Composer Disable Plugin
========================

Allows you to completely disable event listers for some composer plugins under certain conditions.

## Configuration

In your composer.json `extra` section:

```
"extra": {
    "composer-disable-plugin": {
        "disable-plugins": [
            {
                "plugin-name": "phppro/grumphp",
                "events": [
                    'pre-package-install',
                    'post-package-install',
                    '...',
                    'all',
                ],
                "rules": [
                    {
                        "name": "isPantheon"
                    },
                    {
                        "name": "isCi"
                    },
                    {
                        "name": "codeEvaluatesTrue",
                        "config": {
                            "code": "1==1"
                        }
                    },
                    {
                        "name": "codeEvaluatesFalse",
                        "config": {
                            "code": "1==1"
                        }
                    }
                ],
                "rulesConjunction": "OR" // "AND"
            }
        ]
    }
}
```
