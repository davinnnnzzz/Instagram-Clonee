const fs = require('fs');
const s = fs.readFileSync('c:/xampp/htdocs/Sistem_ig/public/js/script.js','utf8');
const open = (s.match(/{/g)||[]).length;
const close = (s.match(/}/g)||[]).length;
const back = (s.match(/`/g)||[]).length;
console.log('{',open,'}',close,'`',back);
