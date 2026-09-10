const fs = require('fs');
const s = fs.readFileSync('c:/xampp/htdocs/Sistem_ig/public/js/script.js','utf8');
const lines = s.split(/\r?\n/);
const stack = [];
for (let i=0;i<lines.length;i++){
  const line = lines[i];
  for (let j=0;j<line.length;j++){
    const ch = line[j];
    if (ch === '{') stack.push({line:i+1,col:j+1,text:line.trim()});
    else if (ch === '}') stack.pop();
  }
}
if (stack.length===0) console.log('All braces closed'); else console.log('Unclosed braces count',stack.length, 'top:', stack[stack.length-1]);
