#!/usr/bin/env python

import sys
import re
import subprocess

DOTSTART = "[dot]"
DOTEND = "[/dot]"

if __name__ == "__main__":

    content = ""
    
    for line in sys.stdin.readlines():
        content = content + line

    # Replace DOT
    m = 0
    while content.find(DOTSTART) >= 0:
        m = m+1
        pos = content.find(DOTSTART)
        posend = content.find(DOTEND)
        dotdata = content[pos+len(DOTSTART):posend]

        # Titel
        labelMatch = re.search('^(label="([^"]+)")', dotdata, re.MULTILINE)
        title = "Diagramm"
        if labelMatch:
            dotdata = dotdata.replace(labelMatch.groups(0)[0], "")
            title = labelMatch.groups(0)[1]
            title = title.replace("\\n", " ")
        
        dotfile = './media/chart-%d.dot' % m
        pdffile = './media/chart-%d.pdf' % m
        dotfilep = open(dotfile, 'w')
        dotfilep.write(dotdata)
        dotfilep.close()

        latexfig = """\\begin{figure}[htb]
\\begin{center}
\includegraphics[width=\\textwidth]{media/chart-%d.pdf}
\end{center}
\caption{%s}
\label{chart:%d}
\end{figure}

""" % (m, title, m)
        
        subprocess.check_call(["/usr/bin/env", "dot" if dotdata.find("neato") == -1 else "neato", "-Tpdf", "-o", pdffile, dotfile])
        
        content = content[0:pos] + latexfig + content[posend+len(DOTEND):]
       

    # Headlines
    for headline in re.findall("^((#+) ([^\n\r]+))", content, re.MULTILINE):
        content = content.replace(headline[0], "\%s{%s}" % ("section" if len(headline[1]) == 1 else "subsection", headline[2]))

    # Bold
    for bold in re.findall("(__([^_]+)__)", content, re.MULTILINE):
        content = content.replace(bold[0], "\\" + "textbf{%s}" % (bold[1]))

    # Italic
    for italic in re.findall("(_([^_]+)_)", content, re.MULTILINE):
        content = content.replace(italic[0], "\emph{%s}" % (italic[1]))

    # &
    content = content.replace("&", "\\&")

    # Listen
    haslist = False
    newcontent = ""
    style = ""
    for line in content.split("\n"):
        numberItemMatch = re.search("(^(1.| \*) ([^\n\r]+))", line)
        if numberItemMatch:
            if not haslist:
                style = "enumerate" if numberItemMatch.groups(0)[1] == "1." else "itemize"
                newcontent = newcontent + "\\begin{%s}\n" % style
                haslist = True
            newcontent = newcontent + "\\item{%s}\n" % numberItemMatch.groups(0)[2]
        else:
            if haslist:
                newcontent = newcontent + "\\end{%s}\n" % style
                haslist = False
            newcontent = newcontent + line + "\n"
    content = newcontent
        
    sys.stdout.write(content)
