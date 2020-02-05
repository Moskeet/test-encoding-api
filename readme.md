# Test Task Encrypt/Decrypt API
## Requests

####Register 
```text
POST /register
```

| Attribute   | Type     | Required | Description           |
|:------------|:---------|:---------|:----------------------|
| `username`  | string | yes   | Username for registration. |
| `user_public_key` | text | yes   | Public key which you have(this value should be urlencoded).|

Example response:

```json
{}
```
####Get Server Key 
```text
GET /getServerKey
```

| Attribute   | Type     | Required | Description           |
|:------------|:---------|:---------|:----------------------|
| `username`  | string | yes   | Username which you have provided for **/register**. |
| `user_public_key` | text | yes   | User public key which you have provided for **/register** (this value should be urlencoded). |

Example request:

```shell
http://{your-domain.com}/getServerKey?username={your_username}&user_public_key={your_public_key}
-h: Content-Type:application/json
```

Example response:

```json
{
  "server_public_key": "{your_server_public_key}"
}
```
####Store Secret
```text
POST /storeSecret
```

| Attribute   | Type     | Required | Description           |
|:------------|:---------|:---------|:----------------------|
| `username`  | string | yes   | Username which you have provided for **/register**. |
| `user_public_key` | text | yes   | User public key which you have provided for **/register** (this value should be urlencoded). |
| `secret_name`  | string | yes   | Your secret name. |
| `encrypted_secret` | text | yes   | Text for encrypt. |

Example response:

```json
[
  "Your secret encrypted and save!"
]
```
####Get Secret
```text
GET /getServerKey
```

| Attribute   | Type     | Required | Description           |
|:------------|:---------|:---------|:----------------------|
| `username`  | string | yes   | Username which you have provided for **/register**.  |
| `secret_name`  | string | yes   | Secret name which you entered when store secret. |
| `user_public_key` | text | yes   | User public key which you have provided for **/register** (this value should be urlencoded). |

Example request:

```shell
http://{your-domain.com}/getSecret?username={your_username}&user_public_key={your_public_key}=&secret_name={your_secret_name}```
-h: Content-Type:application/json
```
Example response:

```json
{
  "encrypt_text": "{your_encrypted_secret}"
}
```
