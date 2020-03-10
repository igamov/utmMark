# UtmMark

This package for MODX Revolution

This component to save utm parameters from url into placeholders for later use.

It handles utm parameters:
- utm_source
- utm_medium
- utm_term
- utm_content
- utm_campaign
- original_ref
- start_page
- ip
- url
- roistat
- roistat_referrer
- roistat_pos
- yclid

##Usage
Run snippet utmMark in form
```
{$_modx->runSnippet('!utmMark')}
```