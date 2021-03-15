// JSでDBデータをHTML出力する場合のサニタイズ関数
// 参考：http://senoway.hatenablog.com/entry/2013/05/31/235051
function sani(str) {
    str = str.replace(/&/g, "&amp;");
    str = str.replace(/"/g, "&quot;");
    str = str.replace(/'/g, "&#039;");
    str = str.replace(/</g, "&lt;");
    str = str.replace(/>/g, "&gt;");
    return str;
}