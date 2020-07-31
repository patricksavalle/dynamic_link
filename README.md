# Dynamic Link

Crawls and compiles the meta data of an HTML page, using normal meta tags, og:tags and twitter:tags. 
If no image data was used, the website's largest touch-icon is used.

    {exp:dynamic_link url="..."}
        {if crawled}
        {title}
        {description}
        {url}
        {image}
        {description}
        {if:else}
        ...
        {/if}
    {/exp:dynamic_link}

Or for urlencoded URL's

    {exp:dynamic_link url="..." encoded="urlencoded"}
        {if crawled}
        {title}
        {description}
        {url}
        {image}
        {description}
        {/if}
    {/exp:dynamic_link}

Use this to encode an link

    {exp:dynamic_link:encode url="..."}

A non existing url or absent meta-data will result in returning the tag names as values.

Can be used for instance to asynchronously server-side, show a card of an URL as follows:

- create an iframe on the containing page:


    <iframe src="{path='templates/card/{exp:dynamic_link:encode url='...'}}"></iframe>

- and an embedded page, e.g. templares/card 


    <!doctype html>
    <html lang="nl">
    <head>
    </head>
    <body>
    {exp:dynamic_link encoded="base64" url="{segment_3}"}
    <h1>{title}</h1>
    <p>{description}</p>
    <img src="{image}" style="max-width: 100%"/>
    {/exp:dynamic_link}
    </body>
    </html>
    
 - cache the embedded template for higher performance, e.g. 60 minutes 


## Change Log

1.0 Seems to works

## License

I don't care