# Excel to JSON back-end

----

## [Try online](https://csv-to-json-api.vercel.app/?type=csv&source=https%3A%2F%2Fdocs.google.com%2Fspreadsheets%2Fd%2Fe%2F2PACX-1vRprxfriS6WWxSbAhQsZOfON7koY4Fci1j1Biv4Ms0XSiZuIQeHnjzcmpwbEvIY8EdxPqX_PA4Ko9Ky%2Fpub%3Foutput%3Dcsv&headersToSnakeCase=true&filterBy=name&filterOperator=search&filterValue=phone&fromSheetName=&fromSheet=)

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
## WIP

### TODO
- To improve documentation
- Allow to read from POST JSON request
- Select columns to return
- Allow pass `SimpleExcelReader` params like `headersToSnakeCase`
- Create `Vercel` config


### DONE
- ...
