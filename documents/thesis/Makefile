all: Konzept.pdf Aufbau-und-Zeitplan.pdf Thesis.pdf

Konzept.pdf: Konzept.tex
#	-xelatex -interaction=nonstopmode Konzept.tex
#	bibtex Konzept.aux
	-xelatex -interaction=nonstopmode Konzept.tex
	xelatex -interaction=nonstopmode Konzept.tex

Aufbau-und-Zeitplan.pdf: Aufbau-und-Zeitplan.tex
	-xelatex -interaction=nonstopmode Aufbau-und-Zeitplan.tex
	xelatex -interaction=nonstopmode Aufbau-und-Zeitplan.tex

Thesis.pdf: Thesis.tex parts/*.tex parts/*.bib media/*.pdf
	-xelatex -interaction=nonstopmode Thesis.tex
	-bibtex Thesis.aux
	-xelatex -interaction=nonstopmode Thesis.tex
	xelatex -interaction=nonstopmode Thesis.tex

clean:
	-/bin/rm *.lof *.log *.lot *.aux *.bbl *.toc *.blg *.dvi *~ *.out
	-find ./ -type f -name \*.bak | xargs rm
