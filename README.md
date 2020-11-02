# Setup language base
```
LanguageSwitcher::set([
    ['code' => 'en', 'name' => 'English', 'default' => 1],
    ['code' => 'de', 'name' => 'German', 'default' => 0]
]);
```

### Reset language base
```
// Reset language base
LanguageSwitcher::reset();
```

### Check active language
```
// Check active language
LanguageSwitcher::active();
```

### Switch languages
```
// Switch languages
LanguageSwitcher::switch('de');
```

### List all languages
```
// List all languages
LanguageSwitcher::list();
```

### Check if languages isset.

```
// List all languages
LanguageSwitcher::isset();
```


### Append new language.
Append new language with language code. If appended language is default then previous default language will be reset to 0

```
// List all languages
LanguageSwitcher::append([
    'code' => 'fr',
    'name' => 'french',
    'default' => 0 // If 1 then previous default language will be set to 0
]);
```

### Inline stranslation
```
LanguageSwitcher::translate([
    'en' => 'Read more...',
    'de' => 'Weiterlesen...'
]);
```