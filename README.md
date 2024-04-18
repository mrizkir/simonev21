# SIMONEV v21
SIMONEV sesuai dengan permendagri no 98 tahun 2020

# Build
## Node versi 20.12.2
Saat build di node versi ini, terkendala dengan versi openssl baru. Solusinya adalah menggunakan openssl versi lama.
```
export NODE_OPTIONS=--openssl-legacy-provider
```
Edit di package.json, sehingga menjadi seperti berikut:
```
"serve": "vue-cli-service serve --host localhost --openssl-legacy-provider",
"build": "vue-cli-service build --openssl-legacy-provider",
```