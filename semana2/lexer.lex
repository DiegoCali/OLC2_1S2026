\s+                     /* skip whitespace */

[0-9]+(\.[0-9]+)?       return 'num';

"+"                     return '+';
"-"                     return '-';
"*"                     return '*';

"("                     return '(';
")"                     return ')';

<<EOF>>                 return 'EOF';
