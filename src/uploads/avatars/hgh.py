import numpy as np
import matplotlib.pyplot as plt
from matplotlib.widgets import Slider

def mandelbrot_set(width, height, max_iter=256):
    x = np.linspace(-2, 2, width, dtype=np.float32)
    y = np.linspace(-2, 2, height, dtype=np.float32)
    X, Y = np.meshgrid(x, y)
    C = X + 1j * Y
    Z = np.zeros(C.shape, dtype=np.complex64)
    
    iter_count = np.full(C.shape, max_iter, dtype=np.uint8)
    mask = np.ones(C.shape, dtype=bool)
    
    for i in range(max_iter):
        Z[mask] = Z[mask] ** 2 + C[mask]
        mask = np.abs(Z) < 2
        iter_count[mask] = i
    
    return iter_count

width, height = 800, 800
max_iter = 300
colormap = 'inferno'

data = mandelbrot_set(width, height, max_iter)

fig, ax = plt.subplots(figsize=(8, 8))
plt.subplots_adjust(bottom=0.15)
img = ax.imshow(data, cmap=colormap, extent=[-2, 2, -2, 2])
ax.axis("off")

ax_colormap = plt.axes([0.2, 0.05, 0.65, 0.03])
slider_colormap = Slider(ax_colormap, 'Colormap', 0, 4, valinit=0, valstep=1)

colormaps = ['inferno', 'plasma', 'viridis', 'magma', 'cividis']

def update(val):
    img.set_cmap(colormaps[int(slider_colormap.val)])
    fig.canvas.draw_idle()

slider_colormap.on_changed(update)

plt.show()
