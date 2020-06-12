# Setup language base
```
LanguageSwitcher::set([
    ['code' => 'en', 'name' => 'English', 'default' => true],
    ['code' => 'de', 'name' => 'German', 'default' => false]
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