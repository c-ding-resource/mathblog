---
bibliography:
- 'bib.bib'
---
The question is to evaluate
$$
\int_0^\infty\sin(x) \mathrm{d}x
$$
First equation:
$$
\lim_{\alpha \to 0^+} \int_0^\infty \frac{\sin(x)}{x^\alpha}\mathrm{d} x = \lim_{\alpha \to 0^+} \left[ \Gamma(1-\alpha) \cos\left( \frac{\pi \alpha}{2} \right) \right] = 1
$$
The second 
$$
\lim_{\alpha \to 0^+} \int_0^\infty \mathrm{e}^{-\alpha x} \sin(x) \mathrm{d} x = \lim_{\alpha \to 0^+} \frac{1}{\alpha^2 + 1}  = 1
$$
The third
$$
\lim_{\alpha \to 0^+} \int_0^\infty \frac{\sin(x)}{1+\alpha x} \mathrm{d} x = 
   \lim_{\alpha \to 0^+} \left[ \frac{2 \sin \left(\frac{1}{\alpha }\right) \text{Ci}\left(\frac{1}{\alpha
   }\right)+\cos \left(\frac{1}{\alpha }\right) \left(\pi -2
   \text{Si}\left(\frac{1}{\alpha }\right)\right)}{2 \alpha } \right]  = 1
$$
The fourth
$$
\begin{aligned}
\int_{0}^{\infty}\sin\left(x\right)\mathrm{d}x
& = \mathrm{Im}\int_{-\infty}^{\infty}\Theta\left(x\right)\,e^{ix}\mathrm{d}x \\
&= \mathrm{Im}\int_{-\infty}^{\infty}
\left(i