from django.db import models

# Create your models here.
from django.db import models

class Product(models.Model):
    created = models.DateTimeField(auto_now_add=True)
    name = models.CharField(max_length=100)
    category = models.CharField(max_length=100, blank=True, default='')
    description = models.CharField(max_length=100, blank=True, default='')
    brand = models.CharField(max_length=100, blank=True, default='')
    price = models.IntegerField(blank=True)
    class Meta:
        ordering = ('created',)
