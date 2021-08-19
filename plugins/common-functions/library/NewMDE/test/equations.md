---
author:
- 'Ding, C.'
bibliography:
- bibliography.bib
title: Example
---

This is a section
=================

This is an inline equation: $x^2=-1$.

This is a centered equation: $$a^2+b^2=c^2.$$

This is a numbered equation: $$\lim_{n\to\infty}\frac{1}{n}=0.
\label{eq:sample}$$

This is a multiple line equation: $$\begin{aligned}
    \int_{0}^{1}2x\,dx 
    &=x^2\Big|_{x=0}^1\label{eq:sample2}\\
    &=1\notag\end{aligned}$$ $$\begin{aligned}
    \int_{0}^{1}2x\,dx 
    &=x^2\Big|_{x=0}^1\label{eq:sample3}\\
    &=1\notag\end{aligned}$$

This is how to cite the above equation: $\eqref{eq:sample}$ &
[\[eq:sample2\]](#eq:sample2){reference-type="ref"
reference="eq:sample2"}.

::: {#thm:sample .theorem}
**Theorem 1**. *This is a theorem environment.*
:::

::: {#corollary .corollary}
**Corollary 2**. *This is a theorem-like environments.*
:::

This is how to cite the above theorem:
[Theorem 1](#thm:sample){reference-type="ref" reference="thm:sample"} &
[Corollary 2](#corollary){reference-type="ref" reference="corollary"}.

::: {.proof}
*Proof.* Here goes the proof. ◻
:::

This is another section
=======================

This is how to cite references: [@latexcompanion], [@einstein].
