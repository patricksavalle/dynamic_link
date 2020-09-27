# Dynamic Link expressionengine plugin / addon

Crawls and compiles the meta data of an HTML page, using normal meta tags, og:tags and twitter:tags. 
If no image data was used, the website's largest touch-icon is used.     Scraped data is cached for 24hrs so this plugin
is fast enough to use inline / synchronously.

    {exp:dynamic_link url="..."}
        {if crawled}
        {link_title}
        {link_description}
        {link_url}
        {link_image}
        {if:else}
        ...
        {/if}
    {/exp:dynamic_link}

Or for urlencoded URL's

    {exp:dynamic_link url="..." encoded="base64"}
        {if crawled}
        {link_title}
        {link_description}
        {link_url}
        {link_image}
        {/if}
    {/exp:dynamic_link}

Set the caching time-to-live in sec. with "cache_ttl". Bypass (and clear) the caching using -1. No parameters means 24hr default.

    {exp:dynamic_link url="..." cache_ttl="600"}

Use this to encode an link (needed when the URL is part of your own URL segments)

    {exp:dynamic_link:encode url="..."}

A non existing url or absent meta-data will result in returning the tag names as values.

Can be used for instance to asynchronously server-side, show a card of an URL as follows:

- create an iframe on the containing page:


    <iframe src="{path='templates/card/{exp:dynamic_link:encode url='...'}}"></iframe>

- and an embedded page, e.g. templares/card 

```html
    <!doctype html>
    <html lang="nl">
    <head>
    </head>
    <body>
    {exp:dynamic_link encoded="base64" url="{segment_3}"}
    <h1>{link_title}</h1>
    <p>{link_description}</p>
    <img src="{link_image}" style="max-width: 100%"/>
    {/exp:dynamic_link}
    </body>
    </html>
```

 - cache the embedded template for higher performance, e.g. 60 minutes 


## Change Log

1.0 Seems to works

1.1 Renamed the variables because they conflicted with EE names, added 24hr caching
    
1.2 Alternative youtube strategy
    
## License

I don't care
