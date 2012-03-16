all: Konzept.pdf Aufbau-und-Zeitplan.pdf

Konzept.pdf: Konzept.tex
#	-xelatex -interaction=nonstopmode Konzept.tex
#	bibtex Konzept.aux
	-xelatex -interaction=nonstopmode Konzept.tex
	xelatex -interaction=nonstopmode Konzept.tex

Aufbau-und-Zeitplan.pdf: Aufbau-und-Zeitplan.tex
	-xelatex -interaction=nonstopmode Aufbau-und-Zeitplan.tex
	xelatex -interaction=nonstopmode Aufbau-und-Zeitplan.tex

clean:
	-/bin/rm *.lof *.log *.lot *.aux *.bbl *.toc *.blg *.dvi
