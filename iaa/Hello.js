/*const x="my fist";
console.log(x);*/
// server.mjs
//import { createServer } from 'node:http';
const http = require(`http`);
const hostname = `127.0.0.1`;
const port = 3000;

const server = http.createServer((req, res) => {
res.statusCode = 200;
res.setHeader ( 'Content-Type', 'text/plain' );
res.end('Hello World!\n');
});

// starts a simple http server locally on port 3000
server.listen(port, hostname, () => {
console.log('Listening on 127.0.0.1:3000');
});

// run with `node server.mjs`

7