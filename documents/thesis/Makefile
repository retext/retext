all: Konzept.pdf Aufbau-und-Zeitplan.pdf Thesis.pdf

Konzept.pdf: Konzept.tex
#	-xelatex -interaction=nonstopmode Konzept.tex
#	bibtex Konzept.aux
	-xelatex -interaction=nonstopmode Konzept.tex
	xelatex -interaction=nonstopmode Konzept.tex

Aufbau-und-Zeitplan.pdf: Aufbau-und-Zeitplan.tex
	-xelatex -interaction=nonstopmode Aufbau-und-Zeitplan.tex
	xelatex -interaction=nonstopmode Aufbau-und-Zeitplan.tex

Thesis.pdf: Thesis.tex parts/*.tex parts/*.bib media/*.pdf base.tex
	-xelatex -interaction=nonstopmode Thesis.tex
	-bibtex Thesis.aux
	-xelatex -interaction=nonstopmode Thesis.tex
	xelatex -interaction=nonstopmode Thesis.tex

Thesis-kindle.pdf: Thesis-kindle.tex parts/*.tex parts/*.bib media/*.pdf base.tex
	-xelatex -interaction=nonstopmode Thesis-kindle.tex
	-bibtex Thesis-kindle.aux
	-xelatex -interaction=nonstopmode Thesis-kindle.tex
	xelatex -interaction=nonstopmode Thesis-kindle.tex

clean:
	-/bin/rm *.lof *.log *.lot *.aux *.bbl *.toc *.blg *.dvi *~ *.out
	-find ./ -type f -name \*.bak | xargs rm

dropbox:
	if [ -d /mnt/extra/Dropbox/Projekte/retext/Dokumente/Thesis/ ]; then cp Thesis.pdf /mnt/extra/Dropbox/Projekte/retext/Dokumente/Thesis/; fi	
