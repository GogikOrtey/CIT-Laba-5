const express = require('express');
const app = express();
const swaggerUi = require('swagger-ui-dist').absolutePath();

app.use(express.static(swaggerUi));

app.get('/', (req, res) => {
  res.sendFile(`${swaggerUi}/index.html`);
});

app.listen(3000, () => {
  console.log('Listening on http://localhost:3000');
});
