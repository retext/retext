all: Konzept.pdf

Konzept.pdf: Konzept.tex
#	-xelatex -interaction=nonstopmode Konzept.tex
#	bibtex Konzept.aux
	-xelatex -interaction=nonstopmode Konzept.tex
	xelatex -interaction=nonstopmode Konzept.tex

clean:
	-/bin/rm *.lof *.log *.lot *.aux *.bbl *.toc *.blg *.dvi
