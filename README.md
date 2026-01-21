# Excel to JSON back-end

----

## Try online
- [With filter](https://csv-to-json-api.vercel.app/?type=csv&source=https%3A%2F%2Fdocs.google.com%2Fspreadsheets%2Fd%2Fe%2F2PACX-1vRprxfriS6WWxSbAhQsZOfON7koY4Fci1j1Biv4Ms0XSiZuIQeHnjzcmpwbEvIY8EdxPqX_PA4Ko9Ky%2Fpub%3Foutput%3Dcsv&headersToSnakeCase=true&filterBy=name&filterOperator=search&filterValue=phone&fromSheetName=&fromSheet=)
- [Without filter](https://csv-to-json-api.vercel.app/?type=csv&source=https%3A%2F%2Fdocs.google.com%2Fspreadsheets%2Fd%2Fe%2F2PACX-1vRprxfriS6WWxSbAhQsZOfON7koY4Fci1j1Biv4Ms0XSiZuIQeHnjzcmpwbEvIY8EdxPqX_PA4Ko9Ky%2Fpub%3Foutput%3Dcsv&headersToSnakeCase=true)

## Usage

Request demo
> On VSCode, try to use this extension `@id:humao.rest-client`_
```http
GET https://csv-to-json-api.vercel.app/?type=csv
&source=https%3A%2F%2Fdocs.google.com%2Fspreadsheets%2Fd%2Fe%2F2PACX-1vRprxfriS6WWxSbAhQsZOfON7koY4Fci1j1Biv4Ms0XSiZuIQeHnjzcmpwbEvIY8EdxPqX_PA4Ko9Ky%2Fpub%3Foutput%3Dcsv
&headersToSnakeCase=true
&filterBy=name
&filterOperator=search
&filterValue=phone
&fromSheetName=
&fromSheet=
```

#### Accepted params

| Param      | Description | Note | Default value |
| ----------- | ----------- | --- | ----------- |
|`type` | Type of file to be read| `csv`, `xlsx` or `ods` | `csv` |
|`source` | URL of file to be read | | |
|`headersToSnakeCase` | If make lower cased on headers | | `false`|
|`filterBy` | Key (header column) to filter data  | | |
|`filterOperator` | See `List of values to 'filterOperator'` | | search|
|`filterValue` | Value to compare | | |
|`fromSheetName` | Sheet name to get data (when != `type` != `csv`) | | |
|`fromSheet` | Sheet position to get data (when != `type` != `csv`) | | |


* List of values to `filterOperator`:

```sh
'=', 'equal'                                    # =
'!=', 'ne', 'notequal', 'notEqual'              # !=
'>', 'gt'                                       # >
'>=', 'ge'                                      # >=
'<', 'lt'                                       # <
'<=', 'le'                                      # <=
'contains', 'like'                              # compare values case sensitive
'*', 'search', 'ilike'                          # compare values case insensitive
'filled', 'notEmpty',                           # check if values has value
## TODO: # 'length-eq', 'leq', 'length'   # BETA value length is 'eq'
## TODO: # 'length-ne', 'lne'             # BETA value length is 'ne'
## TODO: # 'length-gt', 'lgt'             # BETA value length is 'gt'
## TODO: # 'length-ge', 'lge'             # BETA value length is 'ge'
## TODO: # 'length-lt', 'llt'             # BETA value length is 'lt'
## TODO: # 'length-le', 'lle'             # BETA value length is 'le'
```

#### Filter example #1:
```sh
GET https://csv-to-json-api.vercel.app/?type=csv
&source=...
&filters[0][key]=pergunta         # or
&filters[0][by]=pergunta          # or
&filterBy=pergunta                # or

&filters[0][operator]=contains    # or
&filterOperator=contains          # or

&filters[0][operator]=filled      # or
&filterOperator=filled            # or

&filters[0][value]=abc            #or
&filterValue=abc                  #or
```

#### Filter example #2:
```sh
GET https://csv-to-json-api.vercel.app/?type=csv
&headersToSnakeCase=true
&filters[0][key]=pergunta
&filters[0][operator]=filled
&filters[1][key]=inativo
&filters[1][operator]=ne
&filters[1][value]=sim
```

#### Delimiter/separator example:
```sh
GET https://csv-to-json-api.vercel.app/?type=csv
&delimiter=TAB
&delimiter=SPACE
&delimiter=COMMA
&delimiter=SEMICOLON
&delimiter=PIPE
```

#### Reading TSV file:
```sh
GET https://csv-to-json-api.vercel.app/?type=tsv
&experimental_mode=TRUE
&source=https%3A%2F%2Fdocs.google.com%2Fspreadsheets%2Fd%2Fe%2F2...%26output%3Dtsv

## OR

GET https://csv-to-json-api.vercel.app/?type=csv
&delimiter=TAB
&source=https%3A%2F%2Fdocs.google.com%2Fspreadsheets%2Fd%2Fe%2F2...%26output%3Dtsv
```

----

## Vercel deployment

<a href="https://vercel.com/new/project?template=https://github.com/tiagofrancafernandes/Excel-to-JSON-API-Server/tree/master"><img src="https://vercel.com/button"></a>

[Read more](https://github.com/vercel-community/php)
----

```sh
# Install it globally
npm i -g vercel

# Log in
vercel login

# Let's fly
vercel
```

OR
```sh
# Log in
npx vercel login

# Let's fly
npx vercel
```
-----
## Using Google Spreadsheets
1. Create a copy of this [spreadsheets](https://docs.google.com/spreadsheets/d/1HVlosr3KKFDcZAUTA_QECroLTKXklVU4ZnWcpZySnBc/edit?usp=sharing)
2. Go to Share and select 'Anyone with link'
3. Go to `File` -> `Share` -> `Publish on the web`
    - Select `products` sheet
    - Select `.csv` option
    - Copy the generated URL
    - Encoded the URL like `https://docs.google.com/spreadsheets/d/` to `https%3A%2F%2Fdocs.google.com%2Fspreadsheets%2Fd`
    - Use parsed URL on `source` request key
4. Make request like `https://csv-to-json-api.vercel.app/?type=csv&source=YOUR_URL_HERE`
-----

### URL Encoding

To safely pass a Google Sheets public URL as a query string parameter, you must **URL-encode** it.

#### Original URL

```txt
https://docs.google.com/spreadsheets/d/e/2PACX-1vQn0RIYw6c7JNorQaH2TlQJv27_umansgaIFfnEknNTtTo4IgDGd2KRf6u_-4US1Fya2zNWP2TuCmK3/pub?gid=865796183&single=true&output=tsv
````

#### JavaScript (encode to a single string)

```js
const originalUrl =
  "https://docs.google.com/spreadsheets/d/e/2PACX-1vQn0RIYw6c7JNorQaH2TlQJv27_umansgaIFfnEknNTtTo4IgDGd2KRf6u_-4US1Fya2zNWP2TuCmK3/pub?gid=865796183&single=true&output=tsv";

const encodedUrl = encodeURIComponent(originalUrl);

console.log(encodedUrl);
```

#### Encoded output

```txt
https%3A%2F%2Fdocs.google.com%2Fspreadsheets%2Fd%2Fe%2F2PACX-1vQn0RIYw6c7JNorQaH2TlQJv27_umansgaIFfnEknNTtTo4IgDGd2KRf6u_-4US1Fya2zNWP2TuCmK3%2Fpub%3Fgid%3D865796183%26single%3Dtrue%26output%3Dtsv
```


## WIP
- **[WIP]** Documentation
- **[BETA]** Allow to read from POST JSON request
- **[BETA]** Allow to read and parse TSV files
- **[WIP]** Select columns to return
- **[WIP]** Allow pass `SimpleExcelReader` params/methods/flags like `headersToSnakeCase`

### TODO
<!--  -->
