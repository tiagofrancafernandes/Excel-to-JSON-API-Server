# Excel to JSON back-end

----

## Usage

Request demo
> On VSCode, try to use this extension `@id:humao.rest-client`_
```http
GET http://your-backend-server.com/?type=csv
&source=https%3A%2F%2Fdocs.google.com%2Fspreadsheets%2Fd%2Fe%2F2PACX-1vRprxfriS6WWxSbAhQsZOfON7koY4Fci1j1Biv4Ms0XSiZuIQeHnjzcmpwbEvIY8EdxPqX_PA4Ko9Ky%2Fpub%3Foutput%3Dcsv
&headersToSnakeCase=true
&filterBy=name
&filterOperator=search
&filterValue=phone
&fromSheetName=
&fromSheet=
```

----

## WIP

### TODO
- To improve documentation
- Allow to read from POST JSON request
- Select columns to return
- Allow pass `SimpleExcelReader` params like `headersToSnakeCase`
- Create `Vercel` config


### DONE
- ...
