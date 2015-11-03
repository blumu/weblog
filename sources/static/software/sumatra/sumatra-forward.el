;;; (X)Emacs frontend to forward search with SumatraPDF. This script
;;; is a modified version of the script "kdvi-script.el" by Stefan Kebekus
;;; <kebekus@kde.org>. The modifications were performed by William Blum (http://william.famille-blum.org) 

;; Last modification: 8 October 2008

;;; Tested with ntemacs 23

;;; Requires the tool ddeclient.exe
;;; (http://ftp.gnu.org/old-gnu/emacs/windows/docs/ntemacs/contrib/ddeclient.zip)


;;; This program is free software; you can redistribute it and/or
;;; modify it under the terms of the GNU General Public License as
;;; published by the Free Software Foundation; either version 2 of the
;;; License, or (at your option) any later version.
;;; 
;;; This program is distributed in the hope that it will be useful,
;;; but WITHOUT ANY WARRANTY; without even the implied warranty of
;;; MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
;;; General Public License for more details.
;;; 
;;; You should have received a copy of the GNU General Public License
;;; along with this program; if not, write to the Free Software
;;; Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
;;; 02110-1301, USA.


;;; Insert the following lines in your .emacs file to load this script file automatically
;
;; replace '~/emacs-scripts/' by the path where this script file is stored
;(add-to-list 'load-path (expand-file-name "~/emacs-scripts/"))
;(require 'sumatra-forward)
;(add-hook 'LaTeX-mode-hook (lambda () (local-set-key "\C-x\C-j" 'sumatra-jump-to-line)))
;(add-hook 'tex-mode-hook (lambda () (local-set-key "\C-x\C-j" 'sumatra-jump-to-line)))
;

;;; Set F8 as a shortcut key to perform forward search
(global-set-key [f8] 'sumatra-jump-to-line)


(defvar sumatra-script "sumatra"
  "*Name of start script for sumatra.")

(defun kdvi-get-masterfile (file)
  "Small helper function for AucTeX compatibility.
Converts the special value t that TeX-master might be set to
into a real file name."
  (if (eq file t)
      (buffer-file-name)
    file))

(defun substitute-character (the-string from-car to-car)
  (let ((i (- (length the-string) 1)))
	(while (>= i 0)
	  (if (= (aref the-string i) from-car)
		  (aset the-string i to-car))
	  (setq i (- i 1)))
	the-string))



(defun sumatra-jump-to-line() (interactive)
  (save-excursion
    (let* (;;; current line in file, as found in the documentation
	   ;;; of emacs. Slightly non-intuitive.
	   (current-line (format "%d" (+ 0 (count-lines (point-min) (point)))))
	   ;;; name of the `main' .tex file, which is also used as .dvi basename:
	   (master-file (expand-file-name (if (fboundp 'TeX-master-file)
								    (TeX-master-file t)
								  (kdvi-get-masterfile (kdvi-master-file-name)))))
        ;;; .pdf file name:
	   (pdf-file (concat (file-name-sans-extension master-file) ".pdf"))
	   (pdf-file-dos (substitute-character pdf-file ?/ ?\\))
        ;;; current source file name.
	   (filename (file-relative-name (expand-file-name (buffer-file-name)) (file-name-directory master-file) ))
	   (filename-dos (substitute-character filename ?/ ?\\))
        ;;; DDE message: uncomment one of the following two lines.
		;;; The first one shows SumatraPDF in the foreground, the second keeps it in the background.
		;(dde-message (concat "[ForwardSearch(\"" pdf-file-dos "\",\"" filename-dos "\"," current-line ",0,0,1)]"))
		(dde-message (concat "[ForwardSearch(\"" pdf-file-dos "\",\"" filename-dos "\"," current-line ",0,0,0)]"))        
	   )
      (set-buffer (get-buffer-create " *ddeclient"))
      (erase-buffer)
      (message dde-message)
      (insert dde-message)
      (call-process-region (point-min) (point-max) "ddeclient" t t nil "SUMATRA" "control")
	       ;;  (if (= 0 (string-to-int (buffer-string))) t nil)	      
      )
    )
)

(defun kdvi-master-file-name ()
  "Emulate AucTeX's TeX-master-file function.
Partly copied from tex.el's TeX-master-file and TeX-add-local-master."
  (if (boundp 'TeX-master)
      TeX-master
    (let ((master-file (read-file-name "Master file (default this file): ")))
      (if (y-or-n-p "Save info as local variable? ")
	  (progn
	    (goto-char (point-max))
	    (if (re-search-backward "^\\([^\n]+\\)Local Variables:" nil t)
		(let* ((prefix (if (match-beginning 1)
				   (buffer-substring (match-beginning 1) (match-end 1))
				 ""))
		      (start (point)))
		  (re-search-forward (regexp-quote (concat prefix "End:")) nil t)
		  (if (re-search-backward (regexp-quote (concat prefix "TeX-master")) start t)
		      ;;; if TeX-master line exists already, replace it
		      (progn
			(beginning-of-line 1)
			(kill-line 1))
		    (beginning-of-line 1))
		  (insert prefix "TeX-master: " (prin1-to-string master-file) "\n"))
	      (insert "\n%%% Local Variables: "
;;; mode is of little use without AucTeX ...
;;;		      "\n%%% mode: " (substring (symbol-name major-mode) 0 -5)
		      "\n%%% TeX-master: " (prin1-to-string master-file)
		      "\n%%% End: \n"))
	    (save-buffer)
	    (message "(local variables written.)"))
	(message "(nothing written.)"))
      (set (make-local-variable 'TeX-master) master-file))))

;; ddeclient SUMATRA control [ForwardSearch("D:\my documents\latex\Current\thesis\thesis.pdf","thesis.tex",5,0)]


(provide 'sumatra-forward)

