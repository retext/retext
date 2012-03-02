all: idea.pdf

fetchcontents:
	cat /mnt/extra/Dropbox/Projekte/retext/Dokumente/Konzept\ -\ Executive\ Summary.txt  > idea-contents.txt
	echo "" >> idea-contents.txt
	cat /mnt/extra/Dropbox/Projekte/retext/Dokumente/Konzept\ -\ Texte\ in\ Medienprodukten.txt  >> idea-contents.txt
	echo "" >> idea-contents.txt
	cat /mnt/extra/Dropbox/Projekte/retext/Dokumente/Konzept\ -\ Office-Funktionen.txt  >> idea-contents.txt
	echo "" >> idea-contents.txt
	cat /mnt/extra/Dropbox/Projekte/retext/Dokumente/Konzept\ -\ Beschreibung\ der\ Anwendung.txt  >> idea-contents.txt
	echo "" >> idea-contents.txt
	cat idea-contents.txt | ./markdown-to-latex.py > idea-contents.tex

	cat /mnt/extra/Dropbox/Projekte/retext/Dokumente/Konzept\ -\ Abstract.txt  > idea-abstract.txt
	echo "" >> idea-abstract.txt
	cat idea-abstract.txt | ./markdown-to-latex.py > idea-abstract.tex


idea.pdf: *.tex fetchcontents
#	-xelatex -interaction=nonstopmode idea.tex
#	bibtex idea.aux
	-xelatex -interaction=nonstopmode idea.tex
	xelatex -interaction=nonstopmode idea.tex
	cp idea.pdf /mnt/extra/Dropbox/Projekte/retext/Dokumente/Konzept.pdf

clean:
	-/bin/rm *.lof *.log *.lot *.aux *.bbl *.toc *.blg *.dvi *.txt *.out *~
	-/bin/rm ./media/chart-*
	-/bin/rm idea-abstract.tex idea-contents.tex
	/bin/rm idea.pdf