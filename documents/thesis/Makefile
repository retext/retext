all: Konzept.pdf Aufbau-und-Zeitplan.pdf Thesis.pdf Zusammenfassung.pdf Thesis-Externe.pdf

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
	bibtex Thesis1
	bibtex Thesis2
	-xelatex -interaction=nonstopmode Thesis.tex
	xelatex -interaction=nonstopmode Thesis.tex

Thesis-Externe.pdf: Thesis-Externe.tex parts/*.tex parts/*.bib media/*.pdf base.tex
	-xelatex -interaction=nonstopmode Thesis-Externe.tex
	bibtex Thesis-Externe1
	bibtex Thesis-Externe2
	-xelatex -interaction=nonstopmode Thesis-Externe.tex
	xelatex -interaction=nonstopmode Thesis-Externe.tex

Zusammenfassung.pdf: Zusammenfassung.tex base.tex parts/zusammenfassung.tex
	xelatex -interaction=nonstopmode Zusammenfassung.tex

clean:
	-/bin/rm *.lof *.log *.lot *.aux *.bbl *.toc *.blg *.dvi *~ *.out
	-find ./ -type f -name \*.bak | xargs rm

dropbox:
	if [ -d /mnt/extra/Dropbox/Projekte/retext/Dokumente/Thesis/ ]; then cp Thesis.pdf /mnt/extra/Dropbox/Projekte/retext/Dokumente/Thesis/; fi	
