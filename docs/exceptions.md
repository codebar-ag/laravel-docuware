# Exceptions

- `CodebarAg\DocuWare\Exceptions\UnableToMakeRequest`

This is thrown if you are not authorized to make the request.

---

- `CodebarAg\DocuWare\Exceptions\UnableToProcessRequest`

This is thrown if you passed wrong attributes. For example a file cabinet ID
which does not exist.

---

- `CodebarAg\DocuWare\Exceptions\UnableToLogin`

This exception can only be thrown during the login if the credentials did not
match.

---

- `CodebarAg\DocuWare\Exceptions\UnableToLoginNoCookies`

This exception can only be thrown during the login if there was no cookies in
the response from the api.

---

- `CodebarAg\DocuWare\Exceptions\UnableToFindPassphrase`

This exception can only be thrown during the url making if the passphrase
could not be found.

---

- `CodebarAg\DocuWare\Exceptions\UnableToMakeUrl`

Something is wrong during the URL making.

---

- `CodebarAg\DocuWare\Exceptions\UnableToUpdateFields`

No fields were supplied.

---

- `CodebarAg\DocuWare\Exceptions\UnableToGetDocumentCount`

Something is wrong with the response from getting the document count.

---

- `Illuminate\Http\Client\RequestException`

All other cases if the response is not successfully.
