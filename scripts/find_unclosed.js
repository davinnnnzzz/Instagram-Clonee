const fs = require('fs');
const s = fs.readFileSync('c:/xampp/htdocs/Sistem_ig/public/js/script.js','utf8');
const lines = s.split(/\r?\n/);
let bal = 0;
let maxBal = 0;
let maxLine = 0;
for (let i=0;i<lines.length;i++){
  const line = lines[i];
  for (const ch of line){ if (ch === '{') bal++; else if (ch === '}') bal--; }
  if (bal>maxBal){ maxBal=bal; maxLine=i+1; }
}
console.log('final balance', bal, 'maxBalance', maxBal, 'at line', maxLine);
